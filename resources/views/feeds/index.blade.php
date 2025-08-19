@section('title', '피드 - 북로그')
@section('description', '북로그 사용자들의 최신 독서 기록과 리뷰를 확인해보세요. 다양한 책에 대한 생생한 후기와 추천을 만나보실 수 있습니다.')
@section('keywords', '피드, 독서피드, 책리뷰, 독서기록, 북로그, 도서추천, 독서후기')
@section('og_title', '피드 - 북로그')
@section('og_description', '북로그 사용자들의 최신 독서 기록과 리뷰를 확인해보세요.')

@push('meta')
<!-- JSON-LD Structured Data for Feed -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "독서 피드 - 북로그",
    "description": "북로그 사용자들의 최신 독서 기록과 리뷰",
    "url": "{{ route('feeds.index') }}",
    "mainEntity": {
        "@type": "ItemList",
        "numberOfItems": {{ $feeds->total() }},
        "itemListElement": [
            @foreach($feeds->take(10) as $index => $feed)
            {
                "@type": "ListItem",
                "position": {{ $index + 1 }},
                "item": {
                    "@type": "Article",
                    "name": "{{ $feed->title }}",
                    "author": {
                        "@type": "Person",
                        "name": "{{ $feed->user->name }}"
                    },
                    "datePublished": "{{ $feed->created_at->toISOString() }}",
                    "url": "{{ route('feeds.show', $feed) }}"
                }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }
}
</script>
@endpush

<x-app-layout>
    <!-- 서버 렌더링된 피드 콘텐츠 -->
    <div class="max-w-2xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">최신 독서 피드</h1>
        
        <div class="space-y-6">
            @forelse($feeds as $feed)
                <article class="bg-white rounded-lg shadow-sm border p-6">
                    <!-- 작성자 정보 -->
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex-shrink-0 mr-3">
                            @if($feed->user->profile_photo_url)
                                <img src="{{ $feed->user->profile_photo_url }}" alt="{{ $feed->user->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 bg-gray-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ substr($feed->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                <a href="{{ route('profile', $feed->user->username ?? $feed->user->id) }}" class="hover:text-blue-600">
                                    {{ $feed->user->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500">{{ $feed->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <!-- 피드 제목 -->
                    <h2 class="text-xl font-bold mb-3">
                        <a href="{{ route('feeds.show', $feed) }}" class="hover:text-blue-600">
                            {{ $feed->title }}
                        </a>
                    </h2>
                    
                    <!-- 피드 내용 (요약) -->
                    <div class="prose prose-sm max-w-none text-gray-700 mb-4">
                        {{ Str::limit(strip_tags($feed->content), 200) }}
                    </div>
                    
                    <!-- 이미지 (첫 번째만) -->
                    @if($feed->images->count() > 0)
                        <div class="mb-4">
                            <img src="{{ Storage::url($feed->images->first()->file_path) }}" 
                                 alt="피드 이미지" 
                                 class="rounded-lg max-w-full h-auto max-h-64 object-cover">
                        </div>
                    @endif
                    
                    <!-- 피드 메타 정보 -->
                    @if(isset($feed->meta['book_title']) && isset($feed->meta['author']))
                        <div class="bg-blue-50 rounded-lg p-3 mb-4">
                            <p class="text-sm text-blue-800">
                                <span class="font-semibold">📖 {{ $feed->meta['book_title'] }}</span>
                                <span class="text-blue-600"> by {{ $feed->meta['author'] }}</span>
                            </p>
                        </div>
                    @endif
                    
                    <!-- 액션 버튼들 -->
                    <div class="flex items-center space-x-4 pt-4 border-t border-gray-100">
                        <button class="flex items-center space-x-1 text-gray-500 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span class="text-sm">좋아요</span>
                        </button>
                        <button class="flex items-center space-x-1 text-gray-500 hover:text-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span class="text-sm">댓글</span>
                        </button>
                        <button class="flex items-center space-x-1 text-gray-500 hover:text-green-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            <span class="text-sm">공유</span>
                        </button>
                    </div>
                </article>
            @empty
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">아직 피드가 없습니다</h3>
                    <p class="text-gray-500 mb-4">첫 번째 독서 기록을 남겨보세요!</p>
                </div>
            @endforelse
        </div>
        
        <!-- 페이지네이션 -->
        @if($feeds->hasPages())
            <div class="mt-8">
                {{ $feeds->links() }}
            </div>
        @endif
    </div>

    <!-- Vue.js 컴포넌트로 전환하기 위한 스크립트 -->
    <script>
        // 클라이언트사이드에서 Vue 앱으로 하이드레이션
        window.addEventListener('DOMContentLoaded', function() {
            // 여기서 Vue 컴포넌트로 전환하거나 추가 인터랙션 구현
            console.log('Feed page loaded with server-side rendered content');
        });
    </script>
</x-app-layout>