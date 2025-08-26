@extends('admin.layout')

@section('title', '포스트 관리')
@section('header', '포스트 관리')
@section('description', '시스템에서 생성된 모든 포스트를 관리합니다')

@section('content')
<!-- 상단 통계 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-file-alt text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">전체 포스트</p>
                <p class="text-xl font-bold text-gray-900">{{ $posts->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-magic text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">AI 생성</p>
                @php
                    $aiPostsCount = \App\Models\Post::whereJsonContains('meta->generated_by', 'ai')->count();
                @endphp
                <p class="text-xl font-bold text-gray-900">{{ $aiPostsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
                <i class="fas fa-user-edit text-purple-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">사용자 작성</p>
                <p class="text-xl font-bold text-gray-900">{{ $posts->total() - $aiPostsCount }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">오늘 생성</p>
                @php
                    $todayPosts = \App\Models\Post::whereDate('created_at', today())->count();
                @endphp
                <p class="text-xl font-bold text-gray-900">{{ $todayPosts }}</p>
            </div>
        </div>
    </div>
</div>

<!-- 필터 및 검색 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="포스트 검색..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option>모든 포스트</option>
                <option value="ai">AI 생성</option>
                <option value="user">사용자 작성</option>
                <option value="published">게시됨</option>
                <option value="draft">임시저장</option>
            </select>

            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option>최신순</option>
                <option>오래된순</option>
                <option>인기순</option>
                <option>작성자순</option>
            </select>
        </div>

        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-filter mr-2"></i>
                고급 필터
            </button>
            <form action="{{ route('admin.generate-feeds') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-magic mr-2"></i>
                    AI 피드 생성
                </button>
            </form>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                새 포스트
            </a>
        </div>
    </div>
</div>

<!-- 포스트 목록 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">포스트 목록</h3>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>{{ $posts->firstItem() }}-{{ $posts->lastItem() }}</span>
                <span>/</span>
                <span>{{ $posts->total() }}</span>
            </div>
        </div>
    </div>

    <div class="divide-y divide-gray-200">
        @foreach($posts as $post)
        <div class="p-6 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <!-- 포스트 헤더 -->
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr($post->user->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $post->user->name ?? 'AI Generated' }}
                                </p>
                                @if($post->user)
                                    <span class="text-xs text-gray-500">{{ $post->user->username }}</span>
                                @endif

                                <!-- AI 생성 여부 -->
                                @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-magic mr-1"></i>
                                        AI 생성
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-user-edit mr-1"></i>
                                        사용자 작성
                                    </span>
                                @endif

                                <!-- 상태 -->
                                @php
                                    $statusValue = is_object($post->status) ? $post->status->value : $post->status;
                                @endphp
                                @if($statusValue === 'published')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        게시됨
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $statusValue }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $post->created_at->format('Y-m-d H:i') }} • {{ $post->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <!-- 포스트 내용 -->
                    <div class="mb-3">
                        <h4 class="text-base font-semibold text-gray-900 mb-2">{{ $post->title }}</h4>
                        <p class="text-sm text-gray-700 line-clamp-3">{{ Str::limit(strip_tags($post->content), 200) }}</p>
                    </div>

                    <!-- 메타 정보 -->
                    @if(isset($post->meta['persona_id']))
                        @php
                            $persona = \App\Models\Persona::find($post->meta['persona_id']);
                        @endphp
                        @if($persona)
                            <div class="flex items-center text-xs text-gray-500 mb-2">
                                <i class="fas fa-user-friends mr-1"></i>
                                <span>페르소나: {{ $persona->name }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $persona->occupation }}</span>
                                @if(isset($post->meta['generated_at']))
                                    <span class="mx-2">•</span>
                                    <span>생성: {{ \Carbon\Carbon::parse($post->meta['generated_at'])->format('Y-m-d H:i') }}</span>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- 해시태그 또는 키워드 -->
                    @php
                        preg_match_all('/#[\w가-힣]+/', $post->content, $hashtags);
                        $hashtags = array_unique($hashtags[0]);
                    @endphp

                    @if(!empty($hashtags))
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach(array_slice($hashtags, 0, 5) as $hashtag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                                    {{ $hashtag }}
                                </span>
                            @endforeach
                            @if(count($hashtags) > 5)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600">
                                    +{{ count($hashtags) - 5 }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- 작업 버튼 -->
                <div class="flex items-center space-x-2 ml-4">
                    <a href="{{ route('admin.posts.show', $post) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors" title="상세보기">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="p-2 text-gray-400 hover:text-green-600 transition-colors" title="편집">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('정말로 이 포스트를 삭제하시겠습니까?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="삭제">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 페이지네이션 -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $posts->links() }}
    </div>
</div>

<!-- AI 생성 포스트 통계 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- 페르소나별 생성 통계 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-pie text-primary-500 mr-2"></i>
            페르소나별 AI 생성 포스트
        </h3>

        @php
            $personaStats = \App\Models\Post::whereNotNull('meta->persona_id')
                ->get()
                ->groupBy('meta.persona_id')
                ->map(function($posts, $personaId) {
                    return [
                        'persona' => \App\Models\Persona::find($personaId),
                        'count' => $posts->count()
                    ];
                })
                ->sortByDesc('count')
                ->take(5);
        @endphp

        @if($personaStats->count() > 0)
            <div class="space-y-3">
                @foreach($personaStats as $stat)
                    @if($stat['persona'])
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-3">
                                    {{ substr($stat['persona']->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $stat['persona']->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $stat['persona']->occupation }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ $stat['count'] }}</p>
                                <p class="text-xs text-gray-500">포스트</p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-chart-pie text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-500">AI 생성 포스트가 없습니다.</p>
            </div>
        @endif
    </div>

    <!-- 최근 활동 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-clock text-primary-500 mr-2"></i>
            최근 활동
        </h3>

        @php
            $recentPosts = \App\Models\Post::with(['user' => function($query) {
                $query->withoutGlobalScopes();
            }])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp

        <div class="space-y-3">
            @foreach($recentPosts as $post)
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                        {{ substr($post->user->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900">
                            <span class="font-medium">{{ $post->user->name ?? 'AI' }}</span>
                            @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
                                <span class="text-green-600">이 AI로 포스트를 생성했습니다</span>
                            @else
                                <span class="text-blue-600">이 포스트를 작성했습니다</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($post->title, 40) }}</p>
                        <p class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
