@section('title', $post->title . ' - ' . $post->user->name . ' - 북로그')
@section('description', $post->content ? Str::limit(strip_tags($post->content), 155) : $post->title . '에 대한 독서 기록을 북로그에서 확인해보세요.')
@section('keywords', $post->title . ', ' . $post->user->name . ', 독서기록, 책리뷰, 피드, 북로그')
@section('og_title', $post->title . ' - ' . $post->user->name)
@section('og_description', $post->content ? Str::limit(strip_tags($post->content), 155) : $post->title . '에 대한 독서 기록')
@section('og_type', 'article')
@section('og_image', $post->images->count() > 0 ? Storage::url($post->images->first()->file_path) : asset('images/og-default.jpg'))

@push('meta')
<!-- JSON-LD Structured Data for Post -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ $post->content ? Str::limit(strip_tags($post->content), 200) : $post->title }}",
    "url": "{{ route('feeds.show', $post) }}",
    @if($post->images->count() > 0)
    "image": "{{ Storage::url($post->images->first()->file_path) }}",
    @else
    "image": "{{ asset('images/og-default.jpg') }}",
    @endif
    "author": {
        "@type": "Person",
        "name": "{{ $post->user->name }}",
        "url": "{{ route('profile', $post->user->username ?? $post->user->id) }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "북로그",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "datePublished": "{{ $post->created_at->toISOString() }}",
    "dateModified": "{{ $post->updated_at->toISOString() }}",
    "articleSection": "독서 기록",
    "inLanguage": "ko-KR",
    @if(isset($post->meta['book_title']) && isset($post->meta['author']))
    "about": {
        "@type": "Book",
        "name": "{{ $post->meta['book_title'] }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->meta['author'] }}"
        }
    },
    @endif
    "interactionStatistic": [
        {
            "@type": "InteractionCounter",
            "interactionType": "https://schema.org/CommentAction",
            "userInteractionCount": "{{ $post->comments->count() }}"
        }
    ]
}
</script>

<!-- Breadcrumb Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "홈",
            "item": "{{ route('home') }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "피드",
            "item": "{{ route('feeds.index') }}"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "{{ $post->title }}",
            "item": "{{ route('feeds.show', $post) }}"
        }
    ]
}
</script>
@endpush

<x-app-layout>
    <!-- 서버 렌더링된 피드 상세 콘텐츠 -->
    <div class="max-w-3xl mx-auto px-4 py-6">
        <!-- 브레드크럼 -->
        <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">홈</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('feeds.index') }}" class="text-blue-600 hover:text-blue-800">피드</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700">{{ Str::limit($post->title, 30) }}</span>
        </nav>

        <!-- 메인 포스트 -->
        <article class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- 포스트 헤더 -->
            <header class="p-6 border-b">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex-shrink-0 mr-4">
                        @if($post->user->profile_photo_url)
                            <img src="{{ $post->user->profile_photo_url }}" 
                                 alt="{{ $post->user->name }}" 
                                 class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-500 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h2 class="font-semibold text-gray-900">
                            <a href="{{ route('profile', $post->user->username ?? $post->user->id) }}" 
                               class="hover:text-blue-600">
                                {{ $post->user->name }}
                            </a>
                        </h2>
                        <div class="flex items-center text-sm text-gray-500">
                            <time datetime="{{ $post->created_at->toISOString() }}">
                                {{ $post->created_at->format('Y년 m월 d일 H:i') }}
                            </time>
                            @if($post->created_at != $post->updated_at)
                                <span class="mx-1">·</span>
                                <span>수정됨</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 포스트 제목 -->
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

                <!-- 도서 정보 -->
                @if(isset($post->meta['book_title']) && isset($post->meta['author']))
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-16 bg-blue-200 rounded mr-3 flex-shrink-0">
                                @if(isset($post->meta['book_cover']))
                                    <img src="{{ $post->meta['book_cover'] }}" 
                                         alt="{{ $post->meta['book_title'] }} 표지" 
                                         class="w-12 h-16 rounded object-cover">
                                @else
                                    <div class="w-12 h-16 bg-blue-300 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-blue-900">{{ $post->meta['book_title'] }}</h3>
                                <p class="text-blue-700 text-sm">{{ $post->meta['author'] }}</p>
                                @if(isset($post->meta['rating']))
                                    <div class="flex items-center mt-1">
                                        <div class="flex text-yellow-500 mr-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $post->meta['rating'])
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-sm text-blue-700">{{ $post->meta['rating'] }}/5</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </header>

            <!-- 포스트 본문 -->
            <div class="p-6">
                <!-- 포스트 이미지들 -->
                @if($post->images->count() > 0)
                    <div class="mb-6">
                        @if($post->images->count() == 1)
                            <img src="{{ Storage::url($post->images->first()->file_path) }}" 
                                 alt="포스트 이미지" 
                                 class="rounded-lg max-w-full h-auto">
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($post->images as $image)
                                    <img src="{{ Storage::url($image->file_path) }}" 
                                         alt="포스트 이미지 {{ $loop->iteration }}" 
                                         class="rounded-lg w-full h-auto object-cover">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- 포스트 내용 -->
                <div class="prose prose-gray max-w-none">
                    {!! nl2br(e($post->content)) !!}
                </div>
            </div>

            <!-- 포스트 액션 -->
            <footer class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <button class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-sm">좋아요</span>
                        </button>
                        <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="text-sm">댓글 {{ $post->comments->count() }}개</span>
                        </button>
                        <button class="flex items-center space-x-2 text-gray-500 hover:text-green-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span class="text-sm">공유</span>
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>
            </footer>
        </article>

        <!-- 댓글 섹션 -->
        <section class="mt-8 bg-white rounded-lg shadow-sm border">
            <header class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">
                    댓글 {{ $post->comments->count() }}개
                </h2>
            </header>

            <div class="p-6">
                @forelse($post->comments as $comment)
                    <article class="flex space-x-4 mb-6 last:mb-0">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0">
                            @if($comment->user->profile_photo_url)
                                <img src="{{ $comment->user->profile_photo_url }}" 
                                     alt="{{ $comment->user->name }}" 
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <h3 class="font-medium text-gray-900">
                                    <a href="{{ route('profile', $comment->user->username ?? $comment->user->id) }}" 
                                       class="hover:text-blue-600">
                                        {{ $comment->user->name }}
                                    </a>
                                </h3>
                                <time class="text-sm text-gray-500" datetime="{{ $comment->created_at->toISOString() }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                </time>
                            </div>
                            <div class="text-gray-700">
                                {!! nl2br(e($comment->content)) !!}
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">아직 댓글이 없습니다</h3>
                        <p class="text-gray-500">첫 번째 댓글을 남겨보세요!</p>
                    </div>
                @endforelse
            </div>

            <!-- 댓글 작성 폼 영역 -->
            <div class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs">
                            ?
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white rounded-lg border px-4 py-2 text-gray-500">
                            댓글을 작성하려면 로그인이 필요합니다
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 뒤로가기 버튼 -->
        <div class="mt-8 text-center">
            <a href="{{ route('feeds.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                피드 목록으로 돌아가기
            </a>
        </div>
    </div>

    <!-- Vue.js 컴포넌트로 전환하기 위한 스크립트 -->
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Feed post page loaded with server-side rendered content');
            
            // 포스트 데이터를 전역으로 설정하여 Vue 앱에서 사용 가능하게 함
            window.postData = @json($post->toArray());
            window.commentsData = @json($post->comments->toArray());
        });
    </script>
</x-app-layout>