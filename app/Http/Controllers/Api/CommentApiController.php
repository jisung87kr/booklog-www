<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Post;
use App\Models\ReadingProcess;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $type, $id)
    {
        try {
            $model = $this->getCommentableModel($type, $id);
            return ApiResponse::success('', $model->comments()->paginate(10));
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $type, $id)
    {
        try {
            $model = $this->getCommentableModel($type, $id);
            $validated = $request->validate([
                'body' => 'required',
            ]);

            if($request->user()){
                $validated['user_id'] = $request->user()->id;
            }

            $comment = $model->comments()->create($validated);
            return ApiResponse::success('', $comment);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        try {
            return ApiResponse::success('', $comment);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        try {
            $validated = $request->validate([
               'body' => 'required'
            ]);
            $comment->update($validated);
            return ApiResponse::success('', $comment);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try {
            $comment->delete();
            return ApiResponse::success('', '');
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function getCommentableModel(string $type, $id)
    {
        switch ($type){
            case 'readingProcess':
                return ReadingProcess::findOrFail($id);
            case 'post':
                return Post::findOrFail($id);
            default:
                abort(404);
        }
    }
}
