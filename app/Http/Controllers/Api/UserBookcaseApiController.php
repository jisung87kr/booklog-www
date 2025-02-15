<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostStatusEnum;
use App\Enums\PostTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Post;
use App\Models\User;
use App\Models\UserBookcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBookcaseApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        try {
            $bookcases = $user->bookcases()->with(['books', 'user'])->where('privacy', false)->paginate(20);
            return response()->success('', $bookcases);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
               'title' => 'required|string|max:255',
               'description' => 'nullable|string',
               'order' => 'nullable|integer',
               'privacy' => 'nullable|boolean',
               'is_default' => 'nullable|boolean',
            ]);

            $bookcase = $user->bookcases()->create($validated);
            $books = request()->input('books');
            $books = collect($books)->mapWithKeys(function($book, $key){
                return [$book => ['order' => $key]];
            });
            $bookcase->books()->attach($books);
            $bookcase->load('books');

            Post::create([
                'ref_key' => $bookcase->id,
                'user_id' => $bookcase->user_id,
                'title' => $bookcase->title,
                'content' => $bookcase->description,
                'type' => PostTypeEnum::BOOKCASE,
                'status' => PostStatusEnum::PUBLISHED,
            ]);

            return response()->success('', $bookcase);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, UserBookcase $bookcase)
    {
        try {
            return response()->success('', $bookcase);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, UserBookcase $bookcase)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'order' => 'nullable|integer',
                'privacy' => 'nullable|boolean',
                'is_default' => 'nullable|boolean',
            ]);

            $bookcase->update($validated);

            $books = request()->input('books');
            $books = collect($books)->mapWithKeys(function($book, $key){
                return [$book => ['order' => $key]];
            });
            $bookcase->books()->sync($books);
            $bookcase->load('books');
            return response()->success('', $bookcase);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, UserBookcase $bookcase)
    {
        try {
            $result = $bookcase->delete();
            return response()->success('', $result);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }

    public function updateOrder(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'bookcases' => 'required|array',
                'bookcases.*.id' => 'required|integer',
                'bookcases.*.order' => 'required|integer',
            ]);

            $bookcases = $validated['bookcases'];

            foreach ($bookcases as $key => $value) {
                $bookcase = UserBookcase::find($value['id']);
                $bookcase->update(['order' => $value['order']]);
            }

            return response()->success('', []);
        } catch(\Exception $e) {
            return response()->error($e->getMessage());
        }
    }
}
