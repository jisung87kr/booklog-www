<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserBookcaseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;
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
Route::get('test', function(\App\Services\Crawler\AladinService $aladinService){
    $result = $aladinService->itemSearch('슬램덩크');
    dd($result);
//    $result = $kyoboBook->getAllBooksByCategory('010101');
//    dd($result);
});

Route::get('/', function () {
    return redirect()->route('home');
})->name('index');

Route::get('/home', function () {
    return view('home');
})->name('home');

// book
Route::get('/books', [BookController::class, 'index'])->name('book.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('book.show');

// users
Route::get('/users', [UserController::class, 'index'])->name('user.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('/users/{user}/books', [UserBookController::class, 'index'])->name('user.book.index');

// search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// profile
Route::get('/@{user}', [UserController::class, 'profile'])->name('profile');

Route::get('/@{user}/bookcases/create', [UserBookcaseController::class, 'create'])->name('bookcase.create');
Route::get('/@{user}/bookcases/{bookcase}', [UserBookcaseController::class, 'show'])->name('bookcase.show');
Route::get('/@{user}/bookcases/{bookcase}/edit', [UserBookcaseController::class, 'edit'])->name('bookcase.edit');

// page
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
})->name('cookie-policy');

Route::get('/report-issue', function () {
    abort(404, '준비중입니다');
})->name('report-issue');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');

    Route::get('/account', [AccountController::class, 'account'])->name('account.index');

    // activity
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
});

