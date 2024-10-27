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
}
