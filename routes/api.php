<?php

use App\Http\Controllers\Api\AuthorApiController;
use App\Http\Controllers\Api\BadgeApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\Api\UserBadgeApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\UserBookApiController;
use App\Http\Controllers\Api\BookProcessApiController;
use App\Http\Controllers\Api\UserBookProcessApiController;
use App\Models\Api\ImageApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/books', [BookApiController::class, 'index'])->name('api.book.index');
    Route::get('/books/{book}', [BookApiController::class, 'show'])->name('api.book.show');
    Route::post('/books', [BookApiController::class, 'store'])->name('api.book.store');
    Route::put('/books/{book}', [BookApiController::class, 'update'])->name('api.book.update');
    Route::delete('/books/{book}', [BookApiController::class, 'destroy'])->name('api.book.destroy');

    Route::get('/users/{user}/books', [UserBookApiController::class, 'index'])->name('api.user.book.index');
    Route::post('/users/{user}/books', [UserBookApiController::class, 'store'])->name('api.user.book.store');
    Route::put('/users/{user}/books', [UserBookApiController::class, 'updateOrder'])->name('api.user.book.updateOrder');
    Route::delete('/users/{user}/books/{book}', [UserBookApiController::class, 'destroy'])->name('api.user.book.destroy');

    Route::get('books/{book}/processes', [BookProcessApiController::Class, 'index'])->name('api.book.process.index');
    Route::get('books/{book}/processes/{process}', [BookProcessApiController::Class, 'show'])->name('api.book.process.show');

    Route::get('users/{user}/books/{book}/processes', [UserBookProcessApiController::Class, 'index'])->name('api.book.process.index');
    Route::get('users/{user}/books/{book}/processes/{process}', [UserBookProcessApiController::Class, 'show'])->name('api.book.process.show');
    Route::post('users/{user}/books/{book}/processes', [UserBookProcessApiController::Class, 'store'])->name('api.book.process.store');
    Route::put('users/{user}/books/{book}/processes/{process}', [UserBookProcessApiController::Class, 'update'])->name('api.book.process.update');
    Route::delete('users/{user}/books/{book}/processes/{process}', [UserBookProcessApiController::Class, 'destroy'])->name('api.book.process.destroy');

    Route::get('/badges', [BadgeApiController::class, 'index'])->name('api.badge.index');
    Route::get('/badges/{badge}', [BadgeApiController::class, 'show'])->name('api.badge.show');
    Route::post('/badges', [BadgeApiController::class, 'store'])->name('api.badge.store');
    Route::put('/badges/{badge}', [BadgeApiController::class, 'update'])->name('api.badge.update');
    Route::delete('/badges/{badge}', [BadgeApiController::class, 'destroy'])->name('api.badge.destroy');

    Route::get('/users/{user}/badges', [UserBadgeApiController::class, 'index'])->name('api.badge.index');
    Route::post('/users/{user}/badges', [UserBadgeApiController::class, 'store'])->name('api.badge.store');
    Route::delete('/users/{user}/badges/{badge}', [UserBadgeApiController::class, 'destroy'])->name('api.badge.destroy');

    Route::resource('categories', CategoryApiController::class)->names('category');
    Route::resource('tags', TagApiController::class)->names('tag');
    Route::resource('authors', AuthorApiController::class)->names('author');

    Route::get('{processes}/{process}/images', [ImageApiController::class, 'index'])->name('process.image.index');
    Route::post('{processes}/{process}/images', [ImageApiController::class, 'store'])->name('process.image.store');
    Route::delete('{processes}/{process}/images/{image}', [ImageApiController::class, 'removeImage'])->name('process.image.removeImage');

    Route::resource('{type}/{id}/comments', CommentApiController::class)->names('comments');
});
