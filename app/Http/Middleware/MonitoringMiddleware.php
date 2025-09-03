<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MonitoringMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        // 성능 메트릭 기록
        $metrics = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'response_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => $endMemory - $startMemory,
            'status_code' => $response->getStatusCode(),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ];
        
        // 느린 요청 감지 (500ms 이상)
        if ($metrics['response_time'] > 500) {
            Log::warning('Slow request detected', $metrics);
        }
        
        // 에러 상태 코드 기록
        if ($response->getStatusCode() >= 400) {
            Log::error('Error response', $metrics);
        }
        
        // 실시간 메트릭 캐시에 저장 (최근 100개 요청)
        $recentRequests = Cache::get('monitoring.recent_requests', []);
        $recentRequests[] = $metrics;
        
        // 최근 100개만 유지
        if (count($recentRequests) > 100) {
            $recentRequests = array_slice($recentRequests, -100);
        }
        
        Cache::put('monitoring.recent_requests', $recentRequests, 3600); // 1시간
        
        // 분당 요청 수 카운트
        $minuteKey = 'monitoring.rpm.' . now()->format('Y-m-d-H-i');
        $currentCount = Cache::get($minuteKey, 0) + 1;
        Cache::put($minuteKey, $currentCount, 120); // 2분간 유지
        
        return $response;
    }
}