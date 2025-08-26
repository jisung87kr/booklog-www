<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Persona;
use App\Models\Post;

class AdminController extends Controller
{

    public function dashboard()
    {
        $stats = [
            'total_users' => User::withoutGlobalScopes()->count(),
            'total_personas' => Persona::count(),
            'total_posts' => Post::count(),
            'ai_generated_posts' => Post::whereJsonContains('meta->generated_by', 'ai')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // 아직 분리하지 않은 메소드들 (필요 시 추가 분리 가능)
    public function generatePersonaFeed()
    {
        // 구현 예정
    }

    public function previewPersonaFeed()
    {
        // 구현 예정
    }
}
