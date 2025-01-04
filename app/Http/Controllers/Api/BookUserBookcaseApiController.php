<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\UserBookcase;
use Illuminate\Http\Request;

class BookUserBookcaseApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserBookcase $bookcase)
    {
        try {
            $books = $bookcase->books()->paginate(20);
            return response()->success('', $books);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UserBookcase $bookcase)
    {
        try {
            $bookcase->books()->attach($request->book_id, [
                'order' => $request->order,
            ]);
            return response()->success('', $bookcase->books);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserBookcase $bookcase, Book $book)
    {
        try {
            $bookcase->books()->updateExistingPivot($book->id, [
                'order' => $request->order,
            ]);

            return response()->success('', $bookcase->books);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserBookcase $bookcase, Book $book)
    {
        try {
            $result = $bookcase->books()->detach($book);
            return response()->success('', $result);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function updateOrder(Request $request, UserBookcase $bookcase)
    {
        try {
            $validated = $request->validate([
                'books' => 'required|array',
                'books.*.id' => 'required|integer',
                'books.*.order' => 'required|integer',
            ]);

            $books = $validated['books'];

            foreach ($books as $key => $value) {
                $bookcase->books()->updateExistingPivot($value['id'], [
                    'order' => $value['order'],
                ]);
            }

            return response()->success('', '');
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }
}
