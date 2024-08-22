<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\UserBookApiController;
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
});
