<?php

use App\Http\Controllers\Api\ActivityApiController;
use App\Http\Controllers\Api\AttachmentApiController;
use App\Http\Controllers\Api\AuthorApiController;
use App\Http\Controllers\Api\BadgeApiController;
use App\Http\Controllers\Api\BookcaseApiController;
use App\Http\Controllers\Api\BookUserBookcaseApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CommentApiController;
use App\Http\Controllers\Api\FeedApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\api\TaggableApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\UserBadgeApiController;
use App\Http\Controllers\Api\UserBookcaseApiController;
use App\Http\Controllers\Api\UserPostApiController;
use App\Models\BookUserBookcase;
use App\Models\UserBookcase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\UserBookApiController;
use App\Http\Controllers\Api\BookProcessApiController;
use App\Http\Controllers\Api\UserBookProcessApiController;
use App\Models\Api\ImageApiController;
use App\Http\Controllers\Api\UserActionApiController;
use App\Http\Responses\ApiResponse;
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
    try {
        return response()->success('', $request->user());
    } catch (\Exception $e) {
        return response()->error('', $e->getMessage());
    }
});


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/books', [BookApiController::class, 'index'])->name('api.book.index');
    Route::get('/books/{book}', [BookApiController::class, 'show'])->name('api.book.show');
    Route::post('/books', [BookApiController::class, 'store'])->name('api.book.store');
    Route::put('/books/{book}', [BookApiController::class, 'update'])->name('api.book.update');
    Route::delete('/books/{book}', [BookApiController::class, 'destroy'])->name('api.book.destroy');

    Route::get('/badges', [BadgeApiController::class, 'index'])->name('api.badge.index');
    Route::get('/badges/{badge}', [BadgeApiController::class, 'show'])->name('api.badge.show');
    Route::post('/badges', [BadgeApiController::class, 'store'])->name('api.badge.store');
    Route::put('/badges/{badge}', [BadgeApiController::class, 'update'])->name('api.badge.update');
    Route::delete('/badges/{badge}', [BadgeApiController::class, 'destroy'])->name('api.badge.destroy');

    Route::resource('/categories', CategoryApiController::class)->names('category');
    Route::resource('/tags', TagApiController::class)->names('tag');
    Route::post('/{type}/{id}/tags', [TaggableApiController::class, 'sync'])->name('taggable.sync');
    Route::resource('/authors', AuthorApiController::class)->names('author');

    Route::post('/{type}/{id}/comments', [CommentApiController::class, 'store'])->name('comment.store');
    Route::put('/comments/{comment}', [CommentApiController::class, 'update'])->name('comment.update');
    Route::delete('/comments/{comment}', [CommentApiController::class, 'destroy'])->name('comment.destroy');

    Route::resource('/posts', PostApiController::class)->names('post')->except(['index']);

    Route::post('/{type}/{id}/images', [ImageApiController::class, 'store'])->name('image.store');
    Route::resource('/{type}/{id}/attachments', AttachmentApiController::class)->names('attachment');

    Route::post('follows', [UserApiController::class, 'follow'])->name('user.follow');
    Route::delete('follows/{user}', [UserApiController::class, 'unFollow'])->name('user.unFollow');

    Route::resource('/users/{user}/actions', UserActionApiController::class)->names('user.action');

    Route::get('/users', [UserApiController::class, 'index'])->name('api.user.index');
    Route::get('/users/{user}/books', [UserBookApiController::class, 'index'])->name('api.user.book.index');
    Route::post('/users/{user}/books', [UserBookApiController::class, 'store'])->name('api.user.book.store');
    Route::put('/users/{user}/books', [UserBookApiController::class, 'updateOrder'])->name('api.user.book.updateOrder');
    Route::delete('/users/{user}/books/{book}', [UserBookApiController::class, 'destroy'])->name('api.user.book.destroy');

    Route::get('/users/{user}/activity/followers', [ActivityApiController::class, 'followers'])->name('activity.follower');
    Route::get('/users/{user}/activity/replies', [ActivityApiController::class, 'replies'])->name('activity.reply');
    Route::get('/users/{user}/activity/mentions', [ActivityApiController::class, 'mentions'])->name('activity.mention');
    Route::get('/users/{user}/activity/quotations', [ActivityApiController::class, 'quotations'])->name('activity.quotation');

    Route::get('/users/{user}/badges', [UserBadgeApiController::class, 'index'])->name('api.badge.index');
    Route::post('/users/{user}/badges', [UserBadgeApiController::class, 'store'])->name('api.badge.store');
    Route::delete('/users/{user}/badges/{badge}', [UserBadgeApiController::class, 'destroy'])->name('api.badge.destroy');

    Route::PUT('/{user}', [UserApiController::class, 'update'])->name('user.update');

    Route::PUT('/users/{user}/bookcases/order', [UserBookcaseApiController::class, 'updateOrder'])->name('user.bookcase.updateOrder');
    Route::resource('/users/{user}/bookcases', UserBookcaseApiController::class)->names('user.bookcase')->except(['index']);

    Route::PUT('/bookcases/{bookcase}/order', [BookUserBookcaseApiController::class, 'updateOrder'])->name('bookcase.updateOrder');
    Route::resource('/bookcases/{bookcase}/books', BookUserBookcaseApiController::class)->names('bookcase.book');
});

Route::get('/feeds', [FeedApiController::class, 'index'])->name('feed.index');
Route::get('/{type}/{id}/comments', [CommentApiController::class, 'index'])->name('comments.index');
Route::get('/comments/{comment}', [CommentApiController::class, 'show'])->name('comments.show');
Route::get('/recommend/users', [UserApiController::class, 'recommend'])->name('recommend.users');
Route::get('/@{user}', [UserApiController::class, 'show'])->name('user.show');
Route::get('/posts', [PostApiController::class, 'index'])->name('post.index');
Route::post('/actions', [UserActionApiController::class, 'store'])->name('action.store');
Route::get('/users/{user}/bookcases', [UserBookcaseApiController::class, 'index'])->name('user.bookcase.index');
