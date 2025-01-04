<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;

class UserBadgeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        try {
            $badges = $user->badges()->paginate(30);
            return response()->success('', $badges);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'badge_id' => 'required',
            ]);

            $user->badges()->attach($validated);
            return response()->success('', $user->badges);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Badge $badge)
    {
        try {
            $user->badges()->detach($badge->id);
            return response()->success('');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
