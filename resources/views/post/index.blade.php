<x-app-layout>
    @section('title', '포스트 목록 - ' . config('app.name'))
    @section('description', '북로그 커뮤니티의 모든 독서 기록과 리뷰를 확인해보세요.')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">


                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-gray-900">포스트</h1>

                            <form action="{{ route('post.index') }}" method="GET">
                                <div class="flex space-x-4">
                                    <div class="flex items-center space-x-4">
                                        <select name="category" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                            <option value="">전체 카테고리</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" @selected($category->id == request()->category)>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <input type="text" name="search" placeholder="검색..."
                                               class="border border-gray-300 rounded-md px-3 py-2 pl-10 text-sm w-64">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if($posts->count() > 0)
                        <div class="space-y-6">
                            @foreach($posts as $post)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                                 alt="{{ $post->user->name }}">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="font-semibold text-gray-900">
                                                    <a href="{{ route('user.show', $post->user->username) }}" class="hover:text-blue-600">
                                                        {{ $post->user->name }}
                                                    </a>
                                                </h3>
                                                <span class="text-gray-500 text-sm">{{ $post->user->username }}</span>
                                                <span class="text-gray-400">•</span>
                                                <time class="text-gray-500 text-sm">{{ $post->formatted_created_at }}</time>

                                                @if($post->type)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                 @if($post->type->value === 'feed') bg-blue-100 text-blue-800
                                                                 @elseif($post->type->value === 'bookcase') bg-green-100 text-green-800
                                                                 @elseif($post->type->value === 'review') bg-purple-100 text-purple-800
                                                                 @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $post->type->value }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-gray-900 mb-3">
                                                @foreach($post->categories as $category)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $category->name }}
                                                </span>
                                                @endforeach
                                                <a href="{{ route('post.show', $post->id) }}" class="hover:text-blue-600">
                                                    {{ strip_tags(Str::limit($post->content, 200)) }}
                                                </a>
                                            </div>

                                            @if($post->images->count() > 0)
                                                <div class="grid grid-cols-2 gap-2 mb-3 max-w-md">
                                                    @foreach($post->images->take(4) as $image)
                                                        <img src="{{ $image->url }}" alt="포스트 이미지"
                                                             class="rounded-md object-cover h-32 w-full">
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($post->bookcase)
                                                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                                    <div class="flex items-center space-x-3">
                                                        @if($post->bookcase->book_cover_image)
                                                            <img src="{{ $post->bookcase->book_cover_image }}" alt="책 표지" class="h-16 w-12 object-cover rounded">
                                                        @endif
                                                        <div>
                                                            <h4 class="font-medium text-gray-900">{{ $post->bookcase->book_title }}</h4>
                                                            @if($post->bookcase->book_author)
                                                                <p class="text-sm text-gray-600">{{ $post->bookcase->book_author }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="flex items-center space-x-6 text-sm text-gray-500">
                                                <button class="flex items-center space-x-1 hover:text-red-600">
                                                    <svg class="h-4 w-4" fill="{{ $post->like_id ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                    <span>{{ $post->like_count ?? 0 }}</span>
                                                </button>

                                                <button class="flex items-center space-x-1 hover:text-blue-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span>{{ $post->comment_count ?? 0 }}</span>
                                                </button>

                                                <button class="flex items-center space-x-1 hover:text-green-600">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                    </svg>
                                                    <span>{{ $post->share_count ?? 0 }}</span>
                                                </button>

                                                @if($post->quote_count > 0)
                                                    <span class="flex items-center space-x-1 text-gray-500">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                        </svg>
                                                        <span>{{ $post->quote_count }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">포스트가 없습니다</h3>
                            <p class="mt-1 text-sm text-gray-500">새로운 포스트를 작성해보세요.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
