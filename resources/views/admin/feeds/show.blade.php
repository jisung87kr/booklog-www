@extends('admin.layout')

@section('title', '포스트 상세보기')
@section('header', '포스트 상세보기')
@section('description', '포스트의 상세 정보를 확인합니다')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- 포스트 헤더 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                        {{ substr($post->user->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $post->user->name ?? 'AI Generated' }}</h3>
                        @if($post->user)
                            <p class="text-sm text-gray-500">@{{ $post->user->username ?? 'username' }}</p>
                        @endif
                        <p class="text-xs text-gray-400">
                            {{ $post->created_at->format('Y년 m월 d일 H시 i분') }}
                            ({{ $post->created_at->diffForHumans() }})
                        </p>
                    </div>
                </div>

                <!-- 상태 및 타입 배지 -->
                <div class="flex items-center space-x-2 mb-4">
                    <!-- AI 생성 여부 -->
                    @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-magic mr-2"></i>
                            AI 생성 포스트
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-user-edit mr-2"></i>
                            사용자 작성
                        </span>
                    @endif

                    <!-- 게시 상태 -->
                    @if($post->status->value === 'published')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-eye mr-2"></i>
                            게시됨
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-draft2digital mr-2"></i>
                            {{ $post->status->value }}
                        </span>
                    @endif

                    <!-- 포스트 타입 -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-tag mr-2"></i>
                        {{ $post->type->value }}
                    </span>
                </div>
            </div>

            <!-- 액션 버튼 -->
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.feeds') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    목록
                </a>
                <form action="{{ route('admin.feeds.destroy', $post) }}" method="POST" class="inline"
                      onsubmit="return confirm('정말로 이 포스트를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        삭제
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 포스트 내용 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

        <div class="prose max-w-none">
            {!! nl2br(e($post->content)) !!}
        </div>
    </div>

    <!-- AI 생성 정보 (AI 포스트인 경우) -->
    @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-green-900 mb-4">
                <i class="fas fa-robot mr-2"></i>
                AI 생성 정보
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($post->meta['persona_id']))
                    @php
                        $persona = \App\Models\Persona::find($post->meta['persona_id']);
                    @endphp
                    @if($persona)
                        <div>
                            <label class="block text-sm font-medium text-green-800 mb-2">사용된 페르소나</label>
                            <div class="flex items-center space-x-3 p-3 bg-white border border-green-200 rounded-lg">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($persona->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $persona->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $persona->occupation }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                @if(isset($post->meta['generated_at']))
                    <div>
                        <label class="block text-sm font-medium text-green-800 mb-2">생성 시간</label>
                        <div class="p-3 bg-white border border-green-200 rounded-lg text-gray-900">
                            {{ \Carbon\Carbon::parse($post->meta['generated_at'])->format('Y년 m월 d일 H시 i분') }}
                        </div>
                    </div>
                @endif

                @if(isset($post->meta['prompt_used']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-green-800 mb-2">사용된 프롬프트</label>
                        <div class="p-3 bg-white border border-green-200 rounded-lg text-gray-700 text-sm">
                            {{ $post->meta['prompt_used'] }}
                        </div>
                    </div>
                @endif

                @if(isset($post->meta['generation_settings']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-green-800 mb-2">생성 설정</label>
                        <div class="p-3 bg-white border border-green-200 rounded-lg">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($post->meta['generation_settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- 메타 정보 -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-info-circle text-primary-500 mr-2"></i>
            메타 정보
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">포스트 ID</label>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-mono">
                    {{ $post->id }}
                </div>
            </div>

            @if($post->parent_id)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">부모 포스트</label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                        <a href="{{ route('admin.feeds.show', $post->parent_id) }}" class="text-primary-600 hover:text-primary-700 font-mono">
                            #{{ $post->parent_id }}
                        </a>
                    </div>
                </div>
            @endif

            @if($post->ref_key)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">참조 키</label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-mono">
                        {{ $post->ref_key }}
                    </div>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">생성일</label>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                    {{ $post->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">수정일</label>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                    {{ $post->updated_at->format('Y-m-d H:i:s') }}
                </div>
            </div>

            @if($post->deleted_at)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">삭제일</label>
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-900">
                        {{ $post->deleted_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- 전체 메타 데이터 (개발자용) -->
        @if($post->meta && count($post->meta) > 0)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">전체 메타 데이터</label>
                <details class="group">
                    <summary class="cursor-pointer p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-700 group-open:rounded-b-none">
                        <span class="text-sm">클릭하여 전체 메타 데이터 보기</span>
                        <i class="fas fa-chevron-down float-right group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="p-3 bg-gray-50 border border-gray-200 border-t-0 rounded-b-lg">
                        <pre class="text-sm text-gray-700 whitespace-pre-wrap overflow-x-auto">{{ json_encode($post->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </details>
            </div>
        @endif
    </div>

    <!-- 해시태그 및 키워드 -->
    @php
        preg_match_all('/#[\w가-힣]+/', $post->content, $hashtags);
        $hashtags = array_unique($hashtags[0]);
    @endphp

    @if(!empty($hashtags))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-hashtag text-primary-500 mr-2"></i>
                추출된 해시태그
            </h3>

            <div class="flex flex-wrap gap-2">
                @foreach($hashtags as $hashtag)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $hashtag }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 관련 포스트 (같은 사용자의 다른 포스트) -->
    @if($post->user)
        @php
            $relatedPosts = $post->user->posts()
                ->where('id', '!=', $post->id)
                ->latest()
                ->limit(5)
                ->get();
        @endphp

        @if($relatedPosts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-link text-primary-500 mr-2"></i>
                    같은 사용자의 다른 포스트
                </h3>

                <div class="space-y-3">
                    @foreach($relatedPosts as $relatedPost)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $relatedPost->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $relatedPost->created_at->format('Y-m-d') }}
                                    @if(isset($relatedPost->meta['generated_by']) && $relatedPost->meta['generated_by'] === 'ai')
                                        • <i class="fas fa-magic text-green-600"></i> AI 생성
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.feeds.show', $relatedPost) }}"
                               class="text-primary-600 hover:text-primary-700 text-sm">
                                보기 <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
