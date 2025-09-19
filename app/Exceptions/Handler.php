<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // 중요한 에러만 알림 전송
            if ($this->shouldReport($e) && app()->environment('production')) {
                //$this->notifyError($e);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->error('record not found.', null, 404);
            }
        });
    }

    /**
     * 에러 알림 전송
     */
    private function notifyError(Throwable $e)
    {
        $errorData = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'url' => request()->fullUrl(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'timestamp' => now()->toDateTimeString()
        ];

        // Slack 웹훅 알림
        if (config('services.slack.webhook_url')) {
            try {
                $this->sendSlackNotification($errorData);
            } catch (\Exception $slackException) {
                Log::error('Failed to send Slack notification', [
                    'error' => $slackException->getMessage()
                ]);
            }
        }

        // 이메일 알림
        if (config('mail.admin_email')) {
            try {
                $this->sendEmailNotification($errorData);
            } catch (\Exception $mailException) {
                Log::error('Failed to send email notification', [
                    'error' => $mailException->getMessage()
                ]);
            }
        }
    }

    /**
     * Slack 알림 전송
     */
    private function sendSlackNotification(array $errorData)
    {
        $webhookUrl = config('services.slack.webhook_url');

        $payload = [
            'text' => '🚨 BookLog 에러 발생',
            'attachments' => [
                [
                    'color' => 'danger',
                    'fields' => [
                        [
                            'title' => 'Error Message',
                            'value' => $errorData['message'],
                            'short' => false
                        ],
                        [
                            'title' => 'File',
                            'value' => $errorData['file'] . ':' . $errorData['line'],
                            'short' => true
                        ],
                        [
                            'title' => 'URL',
                            'value' => $errorData['url'],
                            'short' => true
                        ],
                        [
                            'title' => 'User ID',
                            'value' => $errorData['user_id'] ?: 'Guest',
                            'short' => true
                        ],
                        [
                            'title' => 'Time',
                            'value' => $errorData['timestamp'],
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * 이메일 알림 전송
     */
    private function sendEmailNotification(array $errorData)
    {
        $adminEmail = config('mail.admin_email');

        Mail::raw(
            "BookLog 에러 발생\n\n" .
            "에러 메시지: {$errorData['message']}\n" .
            "파일: {$errorData['file']}:{$errorData['line']}\n" .
            "URL: {$errorData['url']}\n" .
            "사용자 ID: " . ($errorData['user_id'] ?: 'Guest') . "\n" .
            "시간: {$errorData['timestamp']}\n\n" .
            "스택 트레이스:\n{$errorData['trace']}",
            function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                       ->subject('[BookLog] 시스템 에러 알림');
            }
        );
    }
}
