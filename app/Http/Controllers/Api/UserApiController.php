<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function recommend()
    {
        try {
            $recommendedUsers = User::inRandomOrder()->limit(20)->get();
            return ApiResponse::success('', $recommendedUsers);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function follow(Request $request)
    {
        try {
            $result = request()->user()->followings()->create([
                'follow_id' => $request->user()->id,
                'following_id' => $request->input('user_id')
            ]);
            return ApiResponse::success('', $result);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function unFollow(User $user)
    {
        try {
            $result = request()->user()->followings()->delete('following_id', $user->id);
            return ApiResponse::success('', $result);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
