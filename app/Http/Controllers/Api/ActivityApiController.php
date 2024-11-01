<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use Illuminate\Http\Request;

class ActivityApiController extends Controller
{
    public function followers()
    {
        try {
            $followers = request()->user()->followers()->orderBy('id', 'desc')->paginate(10);
            return ApiResponse::success('', $followers);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function replies()
    {
        try {
            $replies = request()->user()->comments()->with('commentable')
                ->where(function ($query) {
                    $query->where('commentable_type', 'App\\Models\\Post');
                })
                ->orWhereIn('id', function ($query) {
                    $query->select('B.id')
                        ->from('comments AS A')
                        ->leftJoin('comments AS B', 'A.id', '=', 'B.parent_id')
                        ->where('B.commentable_type', 'App\\Models\\Post')
                        ->whereNotNull('B.parent_id')
                        ->where('A.user_id', request()->user()->id);
                })
                ->orderBy('id', 'desc')
                ->paginate(10);
            return ApiResponse::success('', $replies);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function mentions()
    {
        try {
            $mentions = request()->user()->mentions()->with('Post')->orderBy('id', 'desc')->paginate(10);
            return ApiResponse::success('', $mentions);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function quotations()
    {
        try {
            $quotations = request()->user()->quotations()->orderBy('id', 'desc')->paginate(10);
            return ApiResponse::success('', $quotations);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
