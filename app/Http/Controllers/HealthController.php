<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class HealthController extends Controller
{
    /**
     * 기본 헬스체크 엔드포인트
     */
    public function check()
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'BookLog',
            'environment' => app()->environment()
        ]);
    }

    /**
     * 상세 헬스체크 (DB, 캐시, 외부 서비스)
     */
    public function detailed()
    {
        $checks = [];
        $overallStatus = 'ok';

        // 데이터베이스 연결 확인
        try {
            DB::connection()->getPdo();
            $userCount = User::count();
            $checks['database'] = [
                'status' => 'ok',
                'connection' => 'connected',
                'users_count' => $userCount
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed'
            ];
            $overallStatus = 'error';
        }

        // 캐시 시스템 확인
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);
            
            $checks['cache'] = [
                'status' => $retrieved === 'test' ? 'ok' : 'error',
                'driver' => config('cache.default')
            ];
        } catch (\Exception $e) {
            $checks['cache'] = [
                'status' => 'error',
                'message' => 'Cache system failed'
            ];
            $overallStatus = 'warning';
        }

        // 스토리지 확인
        try {
            $storageWritable = is_writable(storage_path('logs'));
            $publicWritable = is_writable(public_path('storage'));
            
            $checks['storage'] = [
                'status' => ($storageWritable && $publicWritable) ? 'ok' : 'warning',
                'logs_writable' => $storageWritable,
                'public_writable' => $publicWritable
            ];
        } catch (\Exception $e) {
            $checks['storage'] = [
                'status' => 'error',
                'message' => 'Storage check failed'
            ];
        }

        // 외부 API 상태 확인 (OpenAI)
        if (config('openai.api_key')) {
            $checks['openai'] = [
                'status' => 'ok',
                'configured' => true
            ];
        } else {
            $checks['openai'] = [
                'status' => 'warning',
                'configured' => false,
                'message' => 'OpenAI API key not configured'
            ];
        }

        return response()->json([
            'status' => $overallStatus,
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
            'uptime' => $this->getUptime()
        ]);
    }

    /**
     * 메트릭스 정보
     */
    public function metrics()
    {
        return response()->json([
            'users_total' => User::count(),
            'users_active_today' => User::where('updated_at', '>=', now()->subDay())->count(),
            'posts_total' => \App\Models\Post::count(),
            'books_total' => \App\Models\Book::count(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true)
        ]);
    }

    /**
     * 시스템 업타임 (프로세스 기준)
     */
    private function getUptime()
    {
        if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
            exec('uptime', $output);
            return $output[0] ?? 'Unknown';
        }
        return 'Unable to determine uptime';
    }
}