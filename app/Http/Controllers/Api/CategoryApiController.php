<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::where('parent_id', null)->get();
            return response()->success('', $categories);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
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
                'parent_id' => 'nullable|exists:App\Models\Category,id',
            ]);

            $category = Category::create($validated);
            return response()->success('', $category);
        } catch (QueryException $e) {
            if($e->getCode() == '23000'){
                return response()->error('이미 존재하는 카테고리입니다.', $e->getMessage());
            }
            return response()->error('', $e->getMessage());
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            return response()->success('', $category);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable',
                'parent_id' => 'nullable|exists:App\Models\Category,id',
            ]);

            $category->update($validated);
            return response()->success('', $category);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->success('', '');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
