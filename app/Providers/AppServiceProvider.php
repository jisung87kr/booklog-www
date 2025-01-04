<?php

namespace App\Providers;

use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
