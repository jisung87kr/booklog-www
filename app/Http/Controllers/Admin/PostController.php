<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Image;
use App\Models\Persona;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $posts = Post::with('user')
            ->publishedPosts()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.posts', compact('posts'));
    }

    public function show(Post $post)
    {
        $post->load('user');
        return view('admin.posts.show', compact('post'));
    }

    public function create()
    {
        $users = User::withoutGlobalScopes()->get();
        $personas = Persona::all();
        $categories = Category::where('type', CategoryTypeEnum::POST)->get();
        return view('admin.posts.create', compact('users', 'personas', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'persona_id' => 'nullable|exists:personas,id',
            'status' => 'required|in:draft,published,unpublished',
            'type' => 'required|in:post,bookcase,page,ad',
            'is_ai_generated' => 'boolean',
            'category_ids' => 'nullable|array',
        ]);

        $meta = [];
        if ($request->boolean('is_ai_generated')) {
            $meta['generated_by'] = 'ai';
            $meta['generated_at'] = now()->toISOString();
            if ($request->persona_id) {
                $meta['persona_id'] = $request->persona_id;
            }
        }

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'type' => $request->type,
            'meta' => $meta,
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        // 임시 첨부파일들을 포스트에 연결
        if ($request->has('temp_attachment_ids')) {
            $tempAttachmentIds = json_decode($request->temp_attachment_ids, true);
            if (is_array($tempAttachmentIds)) {
                Attachment::whereIn('id', $tempAttachmentIds)
                    ->where('attachmentable_type', 'temp')
                    ->update([
                        'attachmentable_type' => Post::class,
                        'attachmentable_id' => $post->id,
                    ]);
            }
        }

        $post->syncCategories($request->category_ids ?? []);

        return redirect()->route('admin.posts')->with('success', '포스트가 생성되었습니다.');
    }

    public function edit(Post $post)
    {
        $users = User::withoutGlobalScopes()->get();
        $personas = Persona::all();
        $categories = Category::where('type', CategoryTypeEnum::POST)->get();
        $post->load('images');
        return view('admin.posts.edit', compact('post', 'users', 'personas', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'persona_id' => 'nullable|exists:personas,id',
            'status' => 'required|in:draft,published,unpublished',
            'type' => 'required|in:post,bookcase,page,ad',
            'is_ai_generated' => 'boolean',
            'category_ids' => 'nullable|array',
        ]);

        $meta = $post->meta ?? [];
        if ($request->boolean('is_ai_generated')) {
            $meta['generated_by'] = 'ai';
            if (!isset($meta['generated_at'])) {
                $meta['generated_at'] = now()->toISOString();
            }
            if ($request->persona_id) {
                $meta['persona_id'] = $request->persona_id;
            }
        } else {
            unset($meta['generated_by'], $meta['persona_id']);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'type' => $request->type,
            'meta' => $meta,
            'published_at' => $request->status === 'published' && !$post->published_at ? now() : $post->published_at,
        ]);

        $post->syncCategories($request->category_ids ?? []);

        return redirect()->route('admin.posts')->with('success', '포스트가 수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts')->with('success', '포스트가 삭제되었습니다.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:posts,id',
        ]);

        $deletedCount = Post::whereIn('id', $request->post_ids)->delete();
        return redirect()->back()->with('success', "{$deletedCount}개의 포스트가 삭제되었습니다.");
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif|max:10240', // 10MB max
            'post_id' => 'nullable|exists:posts,id',
        ]);

        try {
            if ($request->post_id) {
                // 기존 포스트에 이미지 추가
                $post = Post::find($request->post_id);
                $image = $this->imageService->storeImage($post, $request->file('image'));
            } else {
                // 임시 이미지 저장 (포스트 생성 전)
                $file = $request->file('image');
                $storagePath = 'public/image';
                $path = $file->store($storagePath);
                list($width, $height) = getimagesize(\Illuminate\Support\Facades\Storage::path($path));

                $image = Image::create([
                    'imageable_type' => 'temp',
                    'imageable_id' => 0,
                    'file_name' => $file->hashName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'sort_order' => 1,
                ]);
            }

            return response()->json([
                'success' => true,
                'image' => [
                    'id' => $image->id,
                    'url' => $image->image_url,
                    'file_name' => $image->file_name,
                    'file_size' => $image->file_size,
                    'width' => $image->width,
                    'height' => $image->height,
                    'sort_order' => $image->sort_order,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '이미지 업로드에 실패했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reorderImages(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'exists:images,id',
        ]);

        try {
            $this->imageService->updateImageOrder($request->image_ids);
            return response()->json([
                'success' => true,
                'message' => '이미지 순서가 업데이트되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '이미지 순서 업데이트에 실패했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage(Image $image)
    {
        try {
            $this->imageService->deleteImage($image->id);
            return response()->json([
                'success' => true,
                'message' => '이미지가 삭제되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '이미지 삭제에 실패했습니다: ' . $e->getMessage()
            ], 500);
        }
    }
}
