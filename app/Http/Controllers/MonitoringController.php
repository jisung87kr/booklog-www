<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    /**
     * 실시간 모니터링 대시보드
     */
    public function dashboard()
    {
        $metrics = $this->getMetrics();
        return view('admin.monitoring.dashboard', compact('metrics'));
    }

    /**
     * API용 메트릭 데이터
     */
    public function api()
    {
        return response()->json($this->getMetrics());
    }

    /**
     * 시스템 메트릭 수집
     */
    private function getMetrics()
    {
        // 최근 요청 데이터
        $recentRequests = Cache::get('monitoring.recent_requests', []);
        
        // 응답 시간 통계
        $responseTimes = array_column($recentRequests, 'response_time');
        $avgResponseTime = count($responseTimes) > 0 ? round(array_sum($responseTimes) / count($responseTimes), 2) : 0;
        $maxResponseTime = count($responseTimes) > 0 ? max($responseTimes) : 0;
        
        // 에러율 계산
        $errorCount = count(array_filter($recentRequests, fn($r) => $r['status_code'] >= 400));
        $errorRate = count($recentRequests) > 0 ? round(($errorCount / count($recentRequests)) * 100, 2) : 0;
        
        // 분당 요청 수
        $currentMinute = now()->format('Y-m-d-H-i');
        $rpm = Cache::get("monitoring.rpm.{$currentMinute}", 0);
        
        // 시스템 리소스
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        return [
            'requests' => [
                'total_recent' => count($recentRequests),
                'per_minute' => $rpm,
                'error_rate' => $errorRate . '%'
            ],
            'performance' => [
                'avg_response_time' => $avgResponseTime . 'ms',
                'max_response_time' => $maxResponseTime . 'ms',
                'memory_usage' => $this->formatBytes($memoryUsage),
                'memory_limit' => $memoryLimit
            ],
            'database' => [
                'users' => \App\Models\User::count(),
                'posts' => \App\Models\Post::count(),
                'books' => \App\Models\Book::count()
            ],
            'recent_requests' => array_slice($recentRequests, -10), // 최근 10개
            'slow_requests' => array_filter($recentRequests, fn($r) => $r['response_time'] > 500),
            'error_requests' => array_filter($recentRequests, fn($r) => $r['status_code'] >= 400)
        ];
    }

    /**
     * 바이트를 읽기 쉬운 형태로 변환
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * 알림 규칙 확인
     */
    public function checkAlerts()
    {
        $metrics = $this->getMetrics();
        $alerts = [];

        // 높은 에러율 체크 (10% 이상)
        if (floatval(str_replace('%', '', $metrics['requests']['error_rate'])) > 10) {
            $alerts[] = [
                'type' => 'error_rate',
                'level' => 'critical',
                'message' => "High error rate: {$metrics['requests']['error_rate']}",
                'timestamp' => now()
            ];
        }

        // 느린 응답 시간 체크 (1초 이상)
        if (floatval(str_replace('ms', '', $metrics['performance']['avg_response_time'])) > 1000) {
            $alerts[] = [
                'type' => 'slow_response',
                'level' => 'warning',
                'message' => "Slow average response time: {$metrics['performance']['avg_response_time']}",
                'timestamp' => now()
            ];
        }

        // 높은 요청 수 체크 (분당 1000건 이상)
        if ($metrics['requests']['per_minute'] > 1000) {
            $alerts[] = [
                'type' => 'high_traffic',
                'level' => 'warning', 
                'message' => "High traffic: {$metrics['requests']['per_minute']} requests per minute",
                'timestamp' => now()
            ];
        }

        // 알림이 있으면 로그에 기록
        if (!empty($alerts)) {
            Log::warning('System alerts triggered', ['alerts' => $alerts]);
        }

        return response()->json(['alerts' => $alerts]);
    }
}