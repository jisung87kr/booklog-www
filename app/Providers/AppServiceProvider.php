<?php

namespace App\Providers;

use App\Http\Responses\ApiResponse;
use App\Services\OpenAi\FunctionHandler;
use App\Services\OpenAi\OpenAiService;
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
            return new OpenAi(config('openai.api_key'));
        });

        app()->bind(FunctionHandler::class, function($app) {
            return new FunctionHandler();
        });

        app()->bind(OpenAiService::class, function($app) {
            return new OpenAiService(
                $app->make(OpenAi::class),
                $app->make(FunctionHandler::class)
            );
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
