<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Author;
use App\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $autho = Author::paginate(30);
            return response()->success('', $autho);
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
                'en_name' => 'nullable',
                'description' => 'nullable',
                'education' => 'nullable',
                'award' => 'nullable',
            ]);

            $author = Author::create($validated);

            if($request->file('file') && $request->file('file')->isValid()){
                $request->validate([
                    'file' => 'required|mimes:jpg,jpeg,png,gif,doc,pdf',
                ]);
                $path = $request->file('file')->store('public/author');
                $author->update([
                    'profile_img' => $path,
                ]);
            }

            return response()->success('', $author);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        try {
            return response()->success('', $author);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable',
                'en_name' => 'nullable',
                'description' => 'nullable',
                'education' => 'nullable',
                'award' => 'nullable',
            ]);

            $author->update($validated);

            if($request->file('file') && $request->file('file')->isValid()){
                $request->validate([
                    'file' => 'required|mimes:jpg,jpeg,png,gif,doc,pdf',
                ]);
                $oldFilePath = $author->profile_path;
                $path = $request->file('file')->store('public/author');
                $author->update([
                    'profile_img' => $path,
                ]);

                Storage::delete($oldFilePath);
            }
            return response()->success('', $author);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        try {
            $author->delete();
            return response()->success('', '');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
