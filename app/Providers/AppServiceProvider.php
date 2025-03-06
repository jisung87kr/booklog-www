<?php

namespace App\Providers;

use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Orhanerday\OpenAi\OpenAi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(OpenAi::class, function($app) {
            return new OpenAi(env('OEPNAI_SECRET_KEY'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($message, $data=null) {
            return ApiResponse::success($message, $data);
        });

        Response::macro('error', function ($message, $data=null, $code = 400) {
            return ApiResponse::error($message, $data, $code);
        });
    }
}
