<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PostApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $filters = request()->all();
            $posts = Post::published()->filter($filters)->paginate(10);
            return response()->success("", $posts);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable',
                'content' => 'required',
                'parent_id' => 'nullable',
                'original_parent_id' => 'nullable',
            ]);

            $validated['status'] = PostStatusEnum::PUBLISHED;

            $post = $request->user()->posts()->create($validated);

            if($request->input('mentions')){
                $userIds = array_map(function($mention){
                    return substr($mention, 1);
                }, $request->input('mentions'));

                $users = \App\Models\User::whereIn('username', $userIds)->get();

                $post->mentions()->sync($users->pluck('id'));
            }

            return response()->success("", $post);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        try {
            return response()->success("", $post);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable',
                'content' => 'nullable',
                'parent_id' => 'nullable',
                'original_parent_id' => 'nullable',
                'status' => ['nullable', Rule::enum(PostStatusEnum::class)]
            ]);
            $post->update($validated);
            return response()->success("", $post);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return response()->success("", '');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
