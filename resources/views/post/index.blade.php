<x-app-layout>
    @section('title', '포스트 목록 - ' . config('app.name'))
    @section('description', '북로그 커뮤니티의 모든 독서 기록과 리뷰를 확인해보세요.')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex space-x-4 justify-center text-lg mb-6">
                <a href="{{ route('post.index') }}" class="px-4 py-2 font-bold">공지사항</a>
                <a href="{{ route('contact.create') }}" class="px-4 py-2 text-gray-500">문의</a>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-gray-900">공지사항</h1>
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
                        <div class="overflow-hidden border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <colgroup>
                                    <col style="width: 60%;">
                                    <col style="width: 20%;">
                                    <col style="width: 10%;">
                                    <col style="width: 10%;">
                                </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">제목</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작성자</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작성일</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">조회수</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($posts as $post)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="mb-1">
                                                            @foreach($post->categories as $category)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                                                    {{ $category->name }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <a href="{{ route('post.show', $post->id) }}" class="hover:text-blue-600">
                                                                {{ $post->title }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <img class="h-8 w-8 rounded-full"
                                                             src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                                             alt="{{ $post->user->name }}">
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <a href="{{ route('profile', $post->user->username) }}" class="hover:text-blue-600">
                                                                {{ $post->user->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ date('Y.m.d', strtotime($post->created_at)) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $post->view_count ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
