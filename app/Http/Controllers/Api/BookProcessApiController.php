<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\ReadingProcess;
use Illuminate\Http\Request;

class BookProcessApiController extends Controller
{
    public function index(Book $book)
    {
        try {
            $processes = $book->processes()->latest()->paginate(30);
            return ApiResponse::success('', $processes);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }

    public function show(Book $book, ReadingProcess $process)
    {
        try {
            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
