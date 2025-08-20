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
@endpush


<x-app-layout>
    <script>
        console.log(@json($post));
    </script>
    <!-- 서버 렌더링된 피드 상세 콘텐츠 -->
    <div class="max-w-3xl mx-auto px-4 py-6">


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
                        <like-button :model="{{ $post }}" :auth="{{ auth()->user() }}" type="post"></like-button>
                        <comment-button :model="{{ $post }}" type="post"></comment-button>
                        <share-button :model="{{ $post }}" type="post"></share-button>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>
            </footer>
        </article>

        <!-- 뒤로가기 버튼 -->
        <div class="mt-8 text-center">
            <a href="{{ route('home') }}"
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
