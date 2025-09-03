<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserBookcaseController;
use App\Models\Post;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
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
//    // 테스트용 페르소나 ID
//    $persona = \App\Models\Persona::find(1);
//    $result = $service->generateContentWithGPT($persona);
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

// Feed routes
Route::get('/feeds/{post}', [App\Http\Controllers\FeedController::class, 'show'])->name('feeds.show');

// profile
Route::get('/@{user}', [UserController::class, 'profile'])->name('profile');

Route::get('/@{user}/bookcases/create', [UserBookcaseController::class, 'create'])->name('bookcase.create');
Route::get('/@{user}/bookcases/{bookcase}', [UserBookcaseController::class, 'show'])->name('bookcase.show');
Route::get('/@{user}/bookcases/{bookcase}/edit', [UserBookcaseController::class, 'edit'])->name('bookcase.edit');

// post
Route::get('posts', [PostController::class, 'index'])->name('post.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('post.show');

// page
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
})->name('cookie-policy');

Route::get('/report-issue', function () {
    abort(404, '준비중입니다');
})->name('report-issue');

// SEO Routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-main.xml', [App\Http\Controllers\SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-users.xml', [App\Http\Controllers\SitemapController::class, 'users'])->name('sitemap.users');
Route::get('/sitemap-books.xml', [App\Http\Controllers\SitemapController::class, 'books'])->name('sitemap.books');
Route::get('/sitemap-posts.xml', [App\Http\Controllers\SitemapController::class, 'posts'])->name('sitemap.posts');

// Health check routes
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check'])->name('health.check');
Route::get('/health/detailed', [App\Http\Controllers\HealthController::class, 'detailed'])->name('health.detailed');
Route::get('/health/metrics', [App\Http\Controllers\HealthController::class, 'metrics'])->name('health.metrics');

// Monitoring routes (admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/monitoring', [App\Http\Controllers\MonitoringController::class, 'dashboard'])->name('monitoring.dashboard');
    Route::get('/monitoring/api', [App\Http\Controllers\MonitoringController::class, 'api'])->name('monitoring.api');
    Route::get('/monitoring/alerts', [App\Http\Controllers\MonitoringController::class, 'checkAlerts'])->name('monitoring.alerts');
});

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
        Route::get('/personas', [App\Http\Controllers\Admin\PersonaController::class, 'index'])->name('personas');
        Route::get('/personas/create', [App\Http\Controllers\Admin\PersonaController::class, 'create'])->name('personas.create');
        Route::post('/personas', [App\Http\Controllers\Admin\PersonaController::class, 'store'])->name('personas.store');
        Route::get('/personas/{persona}/edit', [App\Http\Controllers\Admin\PersonaController::class, 'edit'])->name('personas.edit');
        Route::put('/personas/{persona}', [App\Http\Controllers\Admin\PersonaController::class, 'update'])->name('personas.update');
        Route::delete('/personas/{persona}', [App\Http\Controllers\Admin\PersonaController::class, 'destroy'])->name('personas.destroy');
        Route::post('/personas/{persona}/toggle', [App\Http\Controllers\Admin\PersonaController::class, 'toggle'])->name('personas.toggle');
        Route::post('/personas/schedule', [App\Http\Controllers\Admin\PersonaController::class, 'updateSchedule'])->name('personas.schedule');

        // 사용자 관리
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/assign-persona', [App\Http\Controllers\Admin\UserController::class, 'assignPersona'])->name('users.assign-persona');
        Route::delete('/users/{user}/persona', [App\Http\Controllers\Admin\UserController::class, 'removePersona'])->name('users.remove-persona');
        Route::post('/users/bulk-assign-persona', [App\Http\Controllers\Admin\UserController::class, 'bulkAssignPersona'])->name('users.bulk-assign-persona');

        // 카테고리 관리
        Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/{category}/toggle', [App\Http\Controllers\Admin\CategoryController::class, 'toggle'])->name('categories.toggle');

        // 포스트 관리
        Route::get('/posts', [App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts');
        Route::get('/posts/create', [App\Http\Controllers\Admin\PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [App\Http\Controllers\Admin\PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'show'])->name('posts.show');
        Route::get('/posts/{post}/edit', [App\Http\Controllers\Admin\PostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/bulk-delete', [App\Http\Controllers\Admin\PostController::class, 'bulkDelete'])->name('posts.bulk-delete');

        // 피드 관리
        Route::get('/feeds', [App\Http\Controllers\Admin\FeedController::class, 'index'])->name('feeds');
        Route::get('/feeds/create', [App\Http\Controllers\Admin\FeedController::class, 'create'])->name('feeds.create');
        Route::post('/feeds', [App\Http\Controllers\Admin\FeedController::class, 'store'])->name('feeds.store');
        Route::get('/feeds/{post}', [App\Http\Controllers\Admin\FeedController::class, 'show'])->name('feeds.show');
        Route::get('/feeds/{post}/edit', [App\Http\Controllers\Admin\FeedController::class, 'edit'])->name('feeds.edit');
        Route::put('/feeds/{post}', [App\Http\Controllers\Admin\FeedController::class, 'update'])->name('feeds.update');
        Route::delete('/feeds/{post}', [App\Http\Controllers\Admin\FeedController::class, 'destroy'])->name('feeds.destroy');
        Route::post('/feeds/bulk-delete', [App\Http\Controllers\Admin\FeedController::class, 'bulkDelete'])->name('feeds.bulk-delete');


        // AI 피드 생성
        Route::post('/generate-feeds', [App\Http\Controllers\Admin\PersonaController::class, 'generateFeeds'])->name('generate-feeds');
        Route::post('/generate-feeds/persona/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'generatePersonaFeed'])->name('generate-feeds.persona');
        Route::post('/users/{user}/generate-feed', [App\Http\Controllers\Admin\UserController::class, 'generateFeed'])->name('users.generate-feed');
        Route::get('/feeds/preview/{persona}', [App\Http\Controllers\Admin\AdminController::class, 'previewPersonaFeed'])->name('feeds.preview');

        // 첨부파일 업로드 및 관리
        Route::post('/posts/attachments/upload', [App\Http\Controllers\Admin\AttachmentController::class, 'upload'])->name('posts.attachments.upload');
        Route::post('/posts/attachments/reorder', [App\Http\Controllers\Admin\AttachmentController::class, 'reorderAttachments'])->name('posts.attachments.reorder');
        Route::delete('/posts/attachments/{attachment}', [App\Http\Controllers\Admin\AttachmentController::class, 'delete'])->name('posts.attachments.delete');
        Route::get('/posts/{post}/attachments', [App\Http\Controllers\Admin\AttachmentController::class, 'getPostAttachments'])->name('posts.attachments.get');

        // 에디터 이미지 업로드
        Route::post('/posts/images/upload', [App\Http\Controllers\Admin\AttachmentController::class, 'uploadImage'])->name('posts.images.upload');

        // 이미지 업로드 및 관리
        Route::post('/posts/images/upload', [App\Http\Controllers\Admin\AdminController::class, 'uploadImage'])->name('posts.images.upload');
        Route::post('/posts/images/reorder', [App\Http\Controllers\Admin\AdminController::class, 'reorderImages'])->name('posts.images.reorder');
        Route::delete('/posts/images/{image}', [App\Http\Controllers\Admin\AdminController::class, 'deleteImage'])->name('posts.images.delete');

        // 문의 관리
        Route::get('/contacts', [App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [App\Http\Controllers\Admin\ContactController::class, 'show'])->name('contacts.show');
        Route::put('/contacts/{contact}', [App\Http\Controllers\Admin\ContactController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{contact}', [App\Http\Controllers\Admin\ContactController::class, 'destroy'])->name('contacts.destroy');
    });
});

