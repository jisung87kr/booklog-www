<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class FeedApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = [
                'q' => $request->input('q'),
            ];

            if($request->user()){
                $feeds = Post::publishedFeeds()->filter($filters)->orderBy('id', 'desc')->paginate(40);
            } else {
                $feeds = Post::publishedFeeds()->filter($filters)->orderBy('id', 'desc')->paginate(40);
            }


            if($request->input('qsearch_type') === 'book'){
                $book = Book::filter(['q' => $request->input('q')])->first();
                $feeds = [
                    'feeds' => $feeds,
                    'book' => $book,
                ];
            }

            return response()->success('', $feeds);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->error('', $e->getMessage());
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
