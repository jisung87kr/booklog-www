<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Bookcase;
use App\Models\Post;
use App\Models\ReadingProcess;
use Illuminate\Http\Request;

class FeedApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'type' => 'post',
                'q' => $request->input('q'),
            ];

            if($request->user()){
                $feeds = Post::published()->filter($filters)->orderBy('id', 'desc')->paginate(10);
            } else {
                $feeds = Post::published()->filter($filters)->orderBy('id', 'desc')->paginate(10);
            }
            return ApiResponse::success('', $feeds);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
