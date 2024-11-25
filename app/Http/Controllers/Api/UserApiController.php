<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    public function  index(Request $request)
    {
        try {
            $filter = [
                'q' => $request->query('q'),
            ];
            $users = User::filter($filter)->paginate(30);
            return ApiResponse::success('', $users);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();
            $validated = Validator::make($request->all(), [
                'name' => ['nullable', 'string', 'max:255'],
                'introduction' => ['nullable', 'string', 'max:255'],
                'link' => ['nullable', 'string', 'max:255'],
                'is_secret' => ['nullable', 'boolean'],
            ])->validated();

            if ($request->file('photo')) {
                $user->updateProfilePhoto($request->file('photo'));
            }

            $user->update($validated);
            return ApiResponse::success('', $user);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }

    }

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
            $result = Follow::create([
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
            $result = Follow::where('follow_id', request()->user()->id)->where('following_id', $user->id)->delete();
            return ApiResponse::success('', $result);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
