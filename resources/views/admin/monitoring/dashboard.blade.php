@extends('admin.layout')

@section('content')
    <div class="mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 시스템 모니터링
        </h2>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 요약 카드 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">총 요청 수</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $metrics['requests']['total_recent'] }}</div>
                    <div class="text-sm text-gray-400">최근 100개</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">분당 요청</div>
                    <div class="text-3xl font-bold text-green-600">{{ $metrics['requests']['per_minute'] }}</div>
                    <div class="text-sm text-gray-400">RPM</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">에러율</div>
                    <div class="text-3xl font-bold {{ str_replace('%', '', $metrics['requests']['error_rate']) > 5 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $metrics['requests']['error_rate'] }}
                    </div>
                    <div class="text-sm text-gray-400">Error Rate</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">평균 응답시간</div>
                    <div class="text-3xl font-bold {{ str_replace('ms', '', $metrics['performance']['avg_response_time']) > 1000 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $metrics['performance']['avg_response_time'] }}
                    </div>
                    <div class="text-sm text-gray-400">Average</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 성능 메트릭 -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">⚡ 성능 메트릭</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">최대 응답시간</dt>
                                <dd class="text-sm text-gray-900">{{ $metrics['performance']['max_response_time'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">메모리 사용량</dt>
                                <dd class="text-sm text-gray-900">{{ $metrics['performance']['memory_usage'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">메모리 한계</dt>
                                <dd class="text-sm text-gray-900">{{ $metrics['performance']['memory_limit'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- 데이터베이스 통계 -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">💾 데이터베이스</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">총 사용자</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($metrics['database']['users']) }}명</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">총 게시글</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($metrics['database']['posts']) }}개</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">총 도서</dt>
                                <dd class="text-sm text-gray-900">{{ number_format($metrics['database']['books']) }}권</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- 느린 요청 목록 -->
            @if(!empty($metrics['slow_requests']))
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">🐌 느린 요청 (500ms 이상)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">응답시간</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">메서드</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">시간</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach(array_slice($metrics['slow_requests'], -10) as $request)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $request['url'] }}</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold">{{ $request['response_time'] }}ms</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $request['method'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $request['timestamp'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- 에러 요청 목록 -->
            @if(!empty($metrics['error_requests']))
            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">❌ 에러 요청</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">상태코드</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">메서드</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">시간</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach(array_slice($metrics['error_requests'], -10) as $request)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $request['url'] }}</td>
                                <td class="px-6 py-4 text-sm text-red-600 font-bold">{{ $request['status_code'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $request['method'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $request['timestamp'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- 자동 새로고침 스크립트 -->
            <script>
                // 30초마다 자동 새로고침
                setInterval(() => {
                    fetch('/monitoring/api')
                        .then(response => response.json())
                        .then(data => {
                            // 메트릭 업데이트 로직
                            console.log('Metrics updated:', data);
                        });
                }, 30000);
            </script>
        </div>
    </div>
@endsection