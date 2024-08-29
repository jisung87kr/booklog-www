<?php

namespace App\Http\Controllers\Api;

use App\Enums\TagTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TagApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tags = Tag::paginate(30);
            return ApiResponse::success('', $tags);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
            ]);

            $tag = Tag::create($validated);
            return ApiResponse::success('', $tag);
        } catch (QueryException $e) {
            if($e->getCode() == '23000'){
                return ApiResponse::error('이미 존재하는 태그명입니다.', $e->getMessage());
            }
            return ApiResponse::error('', $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        try {
            return ApiResponse::success('', $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable',
            ]);

            $tag->update($validated);
            return ApiResponse::success('', $tag);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();
            return ApiResponse::success('', '');
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
