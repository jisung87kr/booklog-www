<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Book;
use App\Models\BookUserBookcase;
use App\Models\ReadingProcess;
use App\Models\Tag;
use App\Models\User;
use App\Services\ImageService;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserBookProcessApiController extends Controller
{
    private ImageService $imageService;
    private TagService $tagService;
    public function __construct(ImageService $imageService, TagService $tagService)
    {
        $this->imageService = $imageService;
        $this->tagService = $tagService;
    }
    public function index(User $user, Book $book)
    {
        try {
            $bookcase = BookUserBookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();
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

            DB::beginTransaction();
            $bookcase = BookUserBookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();
            if(!$bookcase){
                $request->user()->books()->attach($book->id);
                $bookcase = BookUserBookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();
            }

            $validated['user_id'] = $user->id;
            $validated['book_id'] = $book->id;
            $process = $bookcase->processes()->create($validated);
            $this->tagService->handleTags($request, $process);
            $this->imageService->storeImages($process, $request->file('images'), $request->all());
            $process = ReadingProcess::findOrFail($process->id);

            DB::commit();
            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            DB::rollBack();
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

            DB::beginTransaction();
            $bookcase = BookUserBookcase::where('user_id', $user->id)->where('book_id', $book->id)->first();
            if (!$bookcase) {
                return ApiResponse::error('Bookcase not found', 404);
            }

            $validated['user_id'] = $user->id;
            $validated['book_id'] = $book->id;
            $process->update($validated);
            $this->tagService->handleTags($request, $process);
            $this->imageService->storeImages($process, $request->file('images'), $request->all());
            $process = ReadingProcess::findOrFail($process->id);

            DB::commit();
            return ApiResponse::success('', $process);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error('', $e->getMessage());
        }
    }


    public function destroy(User $user, Book $book, ReadingProcess $process)
    {
        try {
            foreach ($process->images as $index => $image) {
                Storage::delete($image->file_path);
            }
            $process->delete();
            return ApiResponse::success('');
        } catch (\Exception $e) {
            return ApiResponse::error('', $e->getMessage());
        }
    }
}
