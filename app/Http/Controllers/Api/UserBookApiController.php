<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UserBookApiController extends Controller
{
    public function index(User $user)
    {
        try {
            $books = $user->books()->orderBy('idx')->latest()->paginate(30);
            return ApiResponse::success('', $books);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function store(Request $request, User $user)
    {
        try {
            $request->validate([
                'book_id' => 'required'
            ]);

            $user->books()->attach($request->input('book_id'));
            return ApiResponse::success('', $user->books);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::error('이미 등록된 책입니다.');
            }

            return ApiResponse::error('', $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function updateOrder(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'book.*.id' => 'required',
                'book.*.idx' => 'nullable|int',
            ]);

            foreach ($validated['book'] as $index => $item) {
                if ($user->books()->wherePivot('book_id', $item['id'])->exists()) {
                    $user->books()->updateExistingPivot($item['id'], ['idx' => $item['idx']]);
                }
            }

            return ApiResponse::success('', $user->books);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return ApiResponse::error('이미 등록된 책입니다.');
            }

            return ApiResponse::error('', $e->getMessage());
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function destroy(Request $request, User $user, Book $book)
    {
        try {
            $user->books()->detach($book->id);
            return ApiResponse::success('', $user->books);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
