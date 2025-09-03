<?php

namespace App\Http\Controllers;

use App\Enums\CategoryTypeEnum;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = request()->only(['search', 'category']);
        $posts = Post::publishedPosts()->latest()->paginate(20);
        // $posts comment count eager load

        $categories = Category::where('type', CategoryTypeEnum::POST)->get();
        return view('post.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['comments' => function ($query) {
            $query->withTrashed()->with(['replies' => function ($replyQuery) {
                $replyQuery->withTrashed();
            }]);
        }]);
        return view('post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
