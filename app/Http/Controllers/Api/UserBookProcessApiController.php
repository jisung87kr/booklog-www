<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\Bookcase;
use App\Models\ReadingProcess;
use App\Models\User;
use Illuminate\Http\Request;

class UserBookProcessApiController extends Controller
{
    public function index(User $user, Book $book)
    {
        try {
            $bookcase = Bookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();
            $processes = $bookcase->processes()->latest()->paginate(30);
            return ApiResponse::success('', $processes);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function show(User $user, Book $book, ReadingProcess $process)
    {
        try {
            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function store(Request $request, User $user, Book $book)
    {
        try {
            $validated = $request->validate([
                'current_page' => 'required',
                'note' => 'nullable',
            ]);

            $bookcase = Bookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();

            $validated['user_id'] = $user->id;
            $validated['book_id'] = $book->id;

            $process = $bookcase->processes()->create($validated);
            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function update(Request $request, User $user, Book $book, ReadingProcess $process)
    {
        try {
            $validated = $request->validate([
                'current_page' => 'nullable',
                'note' => 'nullable',
            ]);

            $process->update($validated);

            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function destroy(User $user, Book $book, ReadingProcess $process)
    {
        try {
            $process->delete();
            return ApiResponse::success('');
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
