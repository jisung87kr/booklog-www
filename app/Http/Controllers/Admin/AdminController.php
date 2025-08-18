<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Persona;
use App\Models\Post;
use App\Models\Image;
use App\Services\PersonaFeedService;
use App\Services\ImageService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected PersonaFeedService $feedService;
    protected ImageService $imageService;

    public function __construct(PersonaFeedService $feedService, ImageService $imageService)
    {
        $this->feedService = $feedService;
        $this->imageService = $imageService;
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::withoutGlobalScopes()->count(),
            'total_personas' => Persona::count(),
            'total_posts' => Post::count(),
            'ai_generated_posts' => Post::whereJsonContains('meta->generated_by', 'ai')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function personas()
    {
        $personas = Persona::all();
        
        // 각 페르소나별 사용자 수를 별도로 계산
        foreach ($personas as $persona) {
            $persona->users_count = User::withoutGlobalScopes()->where('persona_id', $persona->id)->count();
        }
        
        return view('admin.personas', compact('personas'));
    }

    public function users()
    {
        $users = User::withoutGlobalScopes()->with('persona')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function posts()
    {
        $posts = Post::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.posts', compact('posts'));
    }

    public function generateFeeds()
    {
        try {
            // 활성화된 페르소나들에 대해 피드 생성
            $personas = Persona::where('is_active', true)->get();
            $generatedCount = 0;
            $errors = [];

            foreach ($personas as $persona) {
                try {
                    // 페르소나에 할당된 사용자가 있는지 확인
                    $user = $persona->users()->inRandomOrder()->first();
                    if ($user) {
                        $this->feedService->generateFeedForPersona($user);
                        $generatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "{$persona->name}: " . $e->getMessage();
                    logger()->error("페르소나 피드 생성 실패", [
                        'persona_id' => $persona->id,
                        'persona_name' => $persona->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "{$generatedCount}개의 AI 피드가 생성되었습니다.";
            if (!empty($errors)) {
                $errorCount = count($errors);
                $message .= " (오류 {$errorCount}건: " . implode(', ', array_slice($errors, 0, 3));
                if ($errorCount > 3) {
                    $message .= " 외 " . ($errorCount - 3) . "건";
                }
                $message .= ")";
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            logger()->error("AI 피드 일괄 생성 실패", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'AI 피드 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    // === 페르소나 관리 ===
    public function createPersona()
    {
        return view('admin.personas.create');
    }

    public function storePersona(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:1|max:120',
            'occupation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaking_style' => 'nullable|string|max:255',
            'reading_preferences' => 'nullable|json',
        ]);

        $readingPreferences = $request->reading_preferences ? 
            json_decode($request->reading_preferences, true) : [];

        Persona::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'occupation' => $request->occupation,
            'description' => $request->description,
            'speaking_style' => $request->speaking_style,
            'reading_preferences' => $readingPreferences,
            'is_active' => true,
        ]);

        return redirect()->route('admin.personas')->with('success', '페르소나가 생성되었습니다.');
    }

    public function editPersona(Persona $persona)
    {
        return view('admin.personas.edit', compact('persona'));
    }

    public function updatePersona(Request $request, Persona $persona)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'age' => 'required|integer|min:1|max:120',
            'occupation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'speaking_style' => 'nullable|string|max:255',
            'reading_preferences' => 'nullable|json',
        ]);

        $readingPreferences = $request->reading_preferences ? 
            json_decode($request->reading_preferences, true) : [];

        $persona->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'age' => $request->age,
            'occupation' => $request->occupation,
            'description' => $request->description,
            'speaking_style' => $request->speaking_style,
            'reading_preferences' => $readingPreferences,
        ]);

        return redirect()->route('admin.personas')->with('success', '페르소나가 업데이트되었습니다.');
    }

    public function destroyPersona(Persona $persona)
    {
        // 페르소나를 사용 중인 사용자가 있는지 확인
        $usersCount = $persona->users()->count();
        
        if ($usersCount > 0) {
            return redirect()->back()->with('error', "이 페르소나를 사용 중인 {$usersCount}명의 사용자가 있어 삭제할 수 없습니다.");
        }

        $persona->delete();
        return redirect()->route('admin.personas')->with('success', '페르소나가 삭제되었습니다.');
    }

    public function togglePersona(Persona $persona)
    {
        $persona->update(['is_active' => !$persona->is_active]);
        
        $status = $persona->is_active ? '활성화' : '비활성화';
        return redirect()->back()->with('success', "페르소나가 {$status}되었습니다.");
    }

    // === 사용자 관리 ===
    public function createUser()
    {
        $personas = Persona::where('is_active', true)->get();
        return view('admin.users.create', compact('personas'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'persona_id' => 'nullable|exists:personas,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'persona_id' => $request->persona_id,
            'email_verified_at' => now(), // 관리자가 생성하는 사용자는 즉시 인증
        ]);

        return redirect()->route('admin.users')->with('success', '새 사용자가 생성되었습니다.');
    }

    public function editUser(User $user)
    {
        // 글로벌 스코프 없이 사용자 로드
        $user = User::withoutGlobalScopes()->find($user->id);
        $personas = Persona::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'personas'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'persona_id' => 'nullable|exists:personas,id',
        ]);

        $user->update($request->only(['name', 'email', 'username', 'persona_id']));

        return redirect()->route('admin.users')->with('success', '사용자 정보가 업데이트되었습니다.');
    }

    public function destroyUser(User $user)
    {
        // 관리자 자신을 삭제하지 못하도록 방지
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', '자신의 계정은 삭제할 수 없습니다.');
        }

        // 사용자와 관련된 데이터 확인
        $postsCount = $user->posts()->count();
        $commentsCount = $user->comments()->count();
        
        if ($postsCount > 0 || $commentsCount > 0) {
            return redirect()->back()->with('error', "이 사용자는 {$postsCount}개의 포스트와 {$commentsCount}개의 댓글을 작성했습니다. 데이터를 먼저 정리한 후 삭제해주세요.");
        }

        $userName = $user->name;
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', "{$userName} 사용자가 삭제되었습니다.");
    }

    public function assignPersona(Request $request, User $user)
    {
        $request->validate([
            'persona_id' => 'required|exists:personas,id',
        ]);

        $persona = Persona::find($request->persona_id);
        $user->update(['persona_id' => $request->persona_id]);

        return redirect()->back()->with('success', "{$user->name}에게 {$persona->name} 페르소나가 할당되었습니다.");
    }

    public function removePersona(User $user)
    {
        $user->update(['persona_id' => null]);
        return redirect()->back()->with('success', "{$user->name}의 페르소나 할당이 해제되었습니다.");
    }

    public function bulkAssignPersona(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'persona_id' => 'required|exists:personas,id',
        ]);

        $persona = Persona::find($request->persona_id);
        $updatedCount = User::withoutGlobalScopes()->whereIn('id', $request->user_ids)
            ->update(['persona_id' => $request->persona_id]);

        return redirect()->back()->with('success', "{$updatedCount}명의 사용자에게 {$persona->name} 페르소나가 할당되었습니다.");
    }

    public function generateUserFeed(User $user)
    {
        if (!$user->persona_id) {
            return redirect()->back()->with('error', '페르소나가 할당되지 않은 사용자입니다.');
        }

        try {
            $this->feedService->generateFeedForPersona($user);
            return redirect()->back()->with('success', "{$user->name}의 AI 피드가 생성되었습니다.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '피드 생성 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    // === 포스트 관리 ===
    public function showPost(Post $post)
    {
        $post->load('user');
        return view('admin.posts.show', compact('post'));
    }

    public function destroyPost(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts')->with('success', '포스트가 삭제되었습니다.');
    }

    public function bulkDeletePosts(Request $request)
    {
        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:posts,id',
        ]);

        $deletedCount = Post::whereIn('id', $request->post_ids)->delete();
        return redirect()->back()->with('success', "{$deletedCount}개의 포스트가 삭제되었습니다.");
    }

    public function createPost()
    {
        $users = User::withoutGlobalScopes()->get();
        $personas = Persona::all();
        return view('admin.posts.create', compact('users', 'personas'));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'persona_id' => 'nullable|exists:personas,id',
            'status' => 'required|in:draft,published,unpublished',
            'type' => 'required|in:post,bookcase,page,ad',
            'is_ai_generated' => 'boolean',
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

        // 임시 이미지들을 포스트에 연결
        if ($request->has('temp_image_ids')) {
            $tempImageIds = json_decode($request->temp_image_ids, true);
            if (is_array($tempImageIds)) {
                Image::whereIn('id', $tempImageIds)
                    ->where('imageable_type', 'temp')
                    ->update([
                        'imageable_type' => Post::class,
                        'imageable_id' => $post->id,
                    ]);
            }
        }

        return redirect()->route('admin.posts')->with('success', '포스트가 생성되었습니다.');
    }

    public function editPost(Post $post)
    {
        $users = User::withoutGlobalScopes()->get();
        $personas = Persona::all();
        $post->load('images');
        return view('admin.posts.edit', compact('post', 'users', 'personas'));
    }

    public function updatePost(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'persona_id' => 'nullable|exists:personas,id',
            'status' => 'required|in:draft,published,unpublished',
            'type' => 'required|in:post,bookcase,page,ad',
            'is_ai_generated' => 'boolean',
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

        return redirect()->route('admin.posts')->with('success', '포스트가 수정되었습니다.');
    }

    // === 이미지 관리 ===
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
