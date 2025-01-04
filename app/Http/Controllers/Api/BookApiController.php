<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function index()
    {
        try {
            $filters = [
                'q' => request()->input('q'),
            ];
            $books = Book::filter($filters)->simplePaginate(30);
            return response()->success('책목록 조회 성공', $books);
        } catch (\Exception $e) {
            return response()->error('책목록 조회 실패', $e->getMessage());
        }
    }

    public function show(Book $book)
    {
        try {
            return response()->success('책조회 성공', $book);
        } catch (ModelNotFoundException $e) {
            return response()->error('책 조회 실패', '해당 책을 찾을 수 없습니다.', 404);
        } catch (\Exception $e) {
            return response()->error('책조회 실패', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'          => ['required'],
                'author'         => ['required'],
                'description'    => ['nullable'],
                'publisher'      => ['nullable'],
                'published_date' => ['nullable'],
                'isbn'           => ['required', 'unique:books'],
                'cover_image'    => ['nullable'],
                'total_pages'    => ['int'],
            ]);
            $book = Book::create($validated);
            return response()->success('', $book);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function update(Request $request, Book $book)
    {
        try {
            $validated = $request->validate([
                'title'          => ['nullable'],
                'author'         => ['nullable'],
                'description'    => ['nullable'],
                'publisher'      => ['nullable'],
                'published_date' => ['nullable'],
                'isbn'           => ['nullable', 'unique:books'],
                'cover_image'    => ['nullable'],
                'total_pages'    => ['nullable', 'int'],
            ]);
            $book->update($validated);
            return response()->success('', $book);
        } catch (\Exception $e) {
            return response()->error('', $e->getMessage());
        }
    }

    public function destroy(Book $book)
    {
        try {
            $book->delete();
            return response()->success('책 삭제 성공');
        } catch (\Exception $e) {
            return response()->error('책 삭제 실패');
        }
    }
}
