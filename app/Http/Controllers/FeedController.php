<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        // 최신 피드 데이터 조회
        $feeds = Post::with(['user', 'images'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // JSON API 요청인 경우 JSON 응답
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $feeds->items(),
                'meta' => [
                    'current_page' => $feeds->currentPage(),
                    'last_page' => $feeds->lastPage(),
                    'total' => $feeds->total()
                ]
            ]);
        }

        // 일반 웹 요청인 경우 서버사이드 렌더링된 HTML 응답
        return view('feeds.index', compact('feeds'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'images', 'comments' => function($query) {
            $query->with('user')->orderBy('created_at', 'desc');
        }]);

        return view('feeds.show', compact('post'));
    }
}