<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityApiController extends Controller
{
    public function followers(User $user)
    {
        try {
            $followers = $user->followers()->orderBy('id', 'desc')->paginate(10);
            return ApiResponse::success('', $followers);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function replies(User $user)
    {
        try {
            $replies = $user->comments()->with('commentable')
                ->where(function ($query) {
                    $query->where('commentable_type', 'App\\Models\\Post');
                })
                ->orWhereIn('id', function ($query) use ($user) {
                    $query->select('B.id')
                        ->from('comments AS A')
                        ->leftJoin('comments AS B', 'A.id', '=', 'B.parent_id')
                        ->where('B.commentable_type', 'App\\Models\\Post')
                        ->whereNotNull('B.parent_id')
                        ->where('A.user_id', $user->id);
                })
                ->orderBy('id', 'desc')
                ->paginate(10);
            return ApiResponse::success('', $replies);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function mentions(User $user)
    {
        try {
            $mentions = $user->mentions()->with('Post')->orderBy('id', 'desc')->paginate(10);
            return ApiResponse::success('', $mentions);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function quotations(User $user)
    {
        try {
            $quotations = Post::whereIn('original_parent_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('posts')
                    ->where('user_id', $user->id);
            })->orderBy('id', 'desc')->paginate(10);

            return ApiResponse::success('', $quotations);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
