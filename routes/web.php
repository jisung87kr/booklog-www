<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserBookcaseController;
use App\Models\Post;
use GuzzleHttp\Psr7\Request;
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
Route::get('test', function(\App\Services\PersonaFeedService $service){
//   dd(env('APP_NAME'));
   $result = $service->generateFeedForPersona(\App\Models\user::find(10));
   dd($result);
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

    // admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        // 페르소나 관리
        Route::get('/personas', [App\Http\Controllers\Admin\AdminController::class, 'personas'])->name('personas');
        Route::get('/personas/create', [App\Http\Controllers\Admin\AdminController::class, 'createPersona'])->name('personas.create');
        Route::post('/personas', [App\Http\Controllers\Admin\AdminController::class, 'storePersona'])->name('personas.store');
        Route::get('/personas/{persona}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editPersona'])->name('personas.edit');
        Route::put('/personas/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'updatePersona'])->name('personas.update');
        Route::delete('/personas/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'destroyPersona'])->name('personas.destroy');
        Route::post('/personas/{persona}/toggle', [App\Http\Controllers\Admin\AdminController::class, 'togglePersona'])->name('personas.toggle');
        Route::post('/personas/schedule', [App\Http\Controllers\Admin\AdminController::class, 'updatePersonaSchedule'])->name('personas.schedule');

        // 사용자 관리
        Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/assign-persona', [App\Http\Controllers\Admin\AdminController::class, 'assignPersona'])->name('users.assign-persona');
        Route::delete('/users/{user}/persona', [App\Http\Controllers\Admin\AdminController::class, 'removePersona'])->name('users.remove-persona');
        Route::post('/users/bulk-assign-persona', [App\Http\Controllers\Admin\AdminController::class, 'bulkAssignPersona'])->name('users.bulk-assign-persona');

        // 포스트 관리
        Route::get('/posts', [App\Http\Controllers\Admin\AdminController::class, 'posts'])->name('posts');
        Route::get('/posts/create', [App\Http\Controllers\Admin\AdminController::class, 'createPost'])->name('posts.create');
        Route::post('/posts', [App\Http\Controllers\Admin\AdminController::class, 'storePost'])->name('posts.store');
        Route::get('/posts/{post}', [App\Http\Controllers\Admin\AdminController::class, 'showPost'])->name('posts.show');
        Route::get('/posts/{post}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editPost'])->name('posts.edit');
        Route::put('/posts/{post}', [App\Http\Controllers\Admin\AdminController::class, 'updatePost'])->name('posts.update');
        Route::delete('/posts/{post}', [App\Http\Controllers\Admin\AdminController::class, 'destroyPost'])->name('posts.destroy');
        Route::post('/posts/bulk-delete', [App\Http\Controllers\Admin\AdminController::class, 'bulkDeletePosts'])->name('posts.bulk-delete');

        // AI 피드 생성
        Route::post('/generate-feeds', [App\Http\Controllers\Admin\AdminController::class, 'generateFeeds'])->name('generate-feeds');
        Route::post('/generate-feeds/persona/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'generatePersonaFeed'])->name('generate-feeds.persona');
        Route::post('/users/{user}/generate-feed', [App\Http\Controllers\Admin\AdminController::class, 'generateUserFeed'])->name('users.generate-feed');
        Route::get('/feeds/preview/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'previewPersonaFeed'])->name('feeds.preview');

        // 이미지 업로드 및 관리
        Route::post('/posts/images/upload', [App\Http\Controllers\Admin\AdminController::class, 'uploadImage'])->name('posts.images.upload');
        Route::post('/posts/images/reorder', [App\Http\Controllers\Admin\AdminController::class, 'reorderImages'])->name('posts.images.reorder');
        Route::delete('/posts/images/{image}', [App\Http\Controllers\Admin\AdminController::class, 'deleteImage'])->name('posts.images.delete');
    });
});

