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
            return response()->success('', $users);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
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
            return response()->success('', $user);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }

    }

    public function recommend()
    {
        try {
            $query = User::inRandomOrder();
            if(request()->user()){
                $query->where('id', '!=', request()->user()->id);
            }
            $recommendedUsers = $query->limit(20)->get();
            return response()->success('', $recommendedUsers);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function follow(Request $request)
    {
        try {
            $result = Follow::create([
                'follow_id' => $request->user()->id,
                'following_id' => $request->input('user_id')
            ]);
            return response()->success('', $result);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function unFollow(User $user)
    {
        try {
            $result = Follow::where('follow_id', request()->user()->id)->where('following_id', $user->id)->delete();
            return response()->success('', $result);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function followers(User $user)
    {
        try {
            $followers = $user->followers()->orderBy('id', 'desc')->paginate(10);
            return response()->success('', $followers);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function followings(User $user)
    {
        try {
            $followers = $user->followings()->orderBy('id', 'desc')->paginate(10);
            return response()->success('', $followers);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
