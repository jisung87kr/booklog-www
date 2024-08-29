<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\BookReadingProcessController;
use App\Http\Controllers\ReadingProcessController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// book
Route::get('/books', [BookController::class, 'index'])->name('book.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('book.show');
Route::get('/books/{book}/processes', [BookReadingProcessController::class, 'index'])->name('book.process.index');
Route::get('/books/{book}/processes/{process}', [BookReadingProcessController::class, 'index'])->name('book.process.show');

// book reading process
Route::get('/processes', [ReadingProcessController::class, 'index'])->name('process.index');
Route::get('/processes/{process}', [ReadingProcessController::class, 'index'])->name('process.show');

// users
Route::get('/users', [UserController::class, 'index'])->name('user.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('/users/{user}/books', [UserBookController::class, 'index'])->name('user.book.index');

// search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/account', [AccountController::class, 'account'])->name('account.index');
});

// home // userBooks // add // readingProcess // account
