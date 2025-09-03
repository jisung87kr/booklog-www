<x-app-layout>
    @section('title', $post->title. '-' . config('app.name'))
    @section('description', Str::limit(strip_tags($post->content), 160))
    @section('og_title', "{$post->user->name}님의 포스트")
    @section('og_description', Str::limit(strip_tags($post->content), 160))
    @section('og_type', 'article')
    @section('og_url', route('post.show', $post->id))
    @if($post->images->first())
        @section('og_image', '{{ $post->images->first()->url }}')
    @endif

    @push('meta')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $post->title }}",
        "description": "{{ Str::limit(strip_tags($post->content), 160) }}",
        "author": {
            "@type": "Person",
            "name": "{{ $post->user->name }}",
            "url": "{{ route('profile', $post->user->username) }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ config('app.name') }}",
            "url": "{{ config('app.url') }}"
        },
        "datePublished": "{{ $post->created_at->toISOString() }}",
        "dateModified": "{{ $post->updated_at->toISOString() }}",
        "url": "{{ route('post.show', $post->id) }}",
        @if($post->images->first())
        "image": "{{ $post->images->first()->url }}",
        @endif
        @if($post->categories->count() > 0)
        "keywords": [
            @foreach($post->categories as $category)
            "{{ $category->name }}"{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        "about": [
            @foreach($post->categories as $category)
            {
                "@type": "Thing",
                "name": "{{ $category->name }}"
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ],
        @endif
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ route('post.show', $post->id) }}"
        },
        "breadcrumb": {
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
                    "name": "포스트 목록",
                    "item": "{{ route('post.index') }}"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "{{ $post->title }}",
                    "item": "{{ route('post.show', $post->id) }}"
                }
            ]
        }
        @if($post->comments->count() > 0)
        ,"comment": [
            @foreach($post->comments->where('parent_id', null) as $comment)
            {
                "@type": "Comment",
                "text": "{{ $comment->body }}",
                "author": {
                    "@type": "Person",
                    "name": "{{ $comment->user->name }}"
                },
                "dateCreated": "{{ $comment->created_at->toISOString() }}"
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
        @endif
    }
    </script>
    @endpush

    <div id="postShow" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol role="list" class="flex items-center space-x-4">
                                <li>
                                    <div>
                                        <a href="{{ route('post.index') }}" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">포스트</span>
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <a href="{{ route('post.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">포스트</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-4 text-sm font-medium text-gray-500" aria-current="page">{{ strip_tags(Str::limit($post->content, 30)) }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <article>
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <img class="h-8 w-8 rounded-full"
                                             src="{{ $post->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                             alt="{{ $post->user->name }}">
                                        <span class="font-medium text-gray-900">{{ $post->user->name }}</span>
                                    </div>
                                    <span class="text-gray-300">|</span>
                                    <time class="text-gray-500 text-sm">{{ date('Y.m.d H:i', strtotime($post->created_at)) }}</time>
                                    <span class="text-gray-300">|</span>
                                    <span class="text-gray-500 text-sm">조회 {{ $post->view_count ?? 0 }}</span>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @foreach($post->categories as $category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="prose max-w-none mb-8">
                            <div class="text-gray-900 leading-relaxed whitespace-pre-wrap text-base">{!! $post->content !!}</div>
                        </div>

                        @if($post->images->count() > 0)
                            <div class="mb-6">
                                @foreach($post->images as $image)
                                    <img src="{{ $image->url }}" alt="첨부 이미지"
                                         class="max-w-full h-auto mb-4 border border-gray-200">
                                @endforeach
                            </div>
                        @endif

                    </article>


                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">댓글 {{ $post->comment_count ?? 0 }}개</h3>

                        @auth
                            <div class="flex space-x-3 mb-6">
                                <img class="h-10 w-10 rounded-full"
                                     src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                     alt="{{ auth()->user()->name }}">
                                <div class="flex-1">
                                            <textarea name="comment" rows="3"
                                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="댓글을 남겨보세요..."></textarea>
                                    <div class="mt-2 flex justify-end">
                                        <button type="submit"
                                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                                                onclick="storeComment()"
                                        >
                                            댓글 작성
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg text-center">
                                <p class="text-gray-600 text-sm">
                                    댓글을 작성하려면
                                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline">로그인</a>이 필요합니다.
                                </p>
                            </div>
                        @endauth

                        @if($post->comments->count() > 0)
                            <div class="space-y-6" id="comments-container">
                                @foreach($post->comments->where('parent_id', null)->take(10) as $comment)
                                    <div class="comment-item parent-comment" data-comment-id="{{ $comment->id }}">
                                        <div class="flex space-x-3">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{ $comment->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                                 alt="{{ $comment->user->name }}">
                                            <div class="flex-1">
                                                <div class="bg-gray-50 rounded-lg p-3">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="font-medium text-sm text-gray-900">{{ $comment->user->name }}</span>
                                                            <span class="text-gray-500 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                                        </div>

                                                        @if(auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole('admin')))
                                                            <div class="flex items-center space-x-2">
                                                                <button onclick="editComment({{ $comment->id }})"
                                                                        class="text-gray-400 hover:text-blue-600 text-xs">
                                                                    수정
                                                                </button>
                                                                <button onclick="deleteComment({{ $comment->id }})"
                                                                        class="text-gray-400 hover:text-red-600 text-xs">
                                                                    삭제
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="comment-content">
                                                        <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                                                    </div>

                                                    <div class="comment-edit-form hidden mt-2">
                                                        <textarea class="w-full border border-gray-300 rounded px-3 py-2 text-sm resize-none">{{ $comment->body }}</textarea>
                                                        <div class="mt-2 flex justify-end space-x-2">
                                                            <button onclick="cancelEditComment({{ $comment->id }})"
                                                                    class="px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                                취소
                                                            </button>
                                                            <button onclick="updateComment({{ $comment->id }})"
                                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                                                저장
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                    @auth
                                                        <button onclick="toggleReplyForm({{ $comment->id }})" class="hover:text-blue-600">답글</button>
                                                    @endauth
{{--                                                    <button class="hover:text-red-600">좋아요</button>--}}
                                                </div>

                                                @auth
                                                    <div id="reply-form-{{ $comment->id }}" class="reply-form hidden mt-3 ml-3">
                                                        <div class="flex space-x-3">
                                                            <img class="h-8 w-8 rounded-full"
                                                                 src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                                                 alt="{{ auth()->user()->name }}">
                                                            <div class="flex-1">
                                                                <textarea rows="2"
                                                                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                                          placeholder="{{ $comment->user->name }}님에게 답글..."></textarea>
                                                                <div class="mt-2 flex justify-end space-x-2">
                                                                    <button onclick="cancelReply({{ $comment->id }})"
                                                                            class="px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                                        취소
                                                                    </button>
                                                                    <button onclick="submitReply({{ $comment->id }})"
                                                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                                                        답글 작성
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endauth

                                                @if($comment->replies->count() > 0)
                                                    <div class="mt-4 ml-6 space-y-3 border-l-2 border-gray-100 pl-4">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="comment-item" data-comment-id="{{ $reply->id }}">
                                                                <div class="flex space-x-2">
                                                                    <div class="flex-shrink-0 relative">
                                                                        <img class="h-8 w-8 rounded-full"
                                                                             src="{{ $reply->user->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                                                             alt="{{ $reply->user->name }}">
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                                                                            <div class="flex items-center justify-between mb-1">
                                                                                <div class="flex items-center space-x-2">
                                                                                    <span class="font-medium text-xs text-gray-900">{{ $reply->user->name }}</span>
                                                                                    <span class="text-gray-500 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                                                                    <span class="text-blue-500 text-xs font-medium">답글</span>
                                                                                </div>

                                                                                @if(auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->hasRole('admin')))
                                                                                    <div class="flex items-center space-x-2">
                                                                                        <button onclick="editComment({{ $reply->id }})"
                                                                                                class="text-gray-400 hover:text-blue-600 text-xs">
                                                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                                            </svg>
                                                                                        </button>
                                                                                        <button onclick="deleteComment({{ $reply->id }})"
                                                                                                class="text-gray-400 hover:text-red-600 text-xs">
                                                                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                                            </svg>
                                                                                        </button>
                                                                                    </div>
                                                                                @endif
                                                                            </div>

                                                                            <div class="comment-content">
                                                                                <p class="text-xs text-gray-700">{{ $reply->body }}</p>
                                                                            </div>

                                                                            <div class="comment-edit-form hidden mt-2">
                                                                                <textarea class="w-full border border-gray-300 rounded px-2 py-1 text-xs resize-none">{{ $reply->body }}</textarea>
                                                                                <div class="mt-2 flex justify-end space-x-2">
                                                                                    <button onclick="cancelEditComment({{ $reply->id }})"
                                                                                            class="px-2 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600">
                                                                                        취소
                                                                                    </button>
                                                                                    <button onclick="updateComment({{ $reply->id }})"
                                                                                            class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                                                                        저장
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

{{--                                                                        <div class="mt-1 flex items-center space-x-3 text-xs text-gray-500">--}}
{{--                                                                            <button class="flex items-center space-x-1 hover:text-red-600">--}}
{{--                                                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">--}}
{{--                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>--}}
{{--                                                                                </svg>--}}
{{--                                                                                <span>좋아요</span>--}}
{{--                                                                            </button>--}}
{{--                                                                        </div>--}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if($post->comments->count() > 10)
                                    <div class="text-center">
                                        <button class="text-sm text-blue-600 hover:underline">
                                            댓글 {{ $post->comments->count() - 10 }}개 더 보기
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 text-sm">첫 번째 댓글을 남겨보세요.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.1.2/dist/axios.min.js"></script>
    <script>
        function storeComment() {
            let postId = {{ $post->id }};
            let comment = document.querySelector('textarea[name="comment"]').value;
            if (!comment.trim()) {
                alert('댓글 내용을 입력해주세요.');
                return;
            }

            axios.post('/api/post/' + postId + '/comments', {
                body: comment,
            }).then(response => {
                alert('댓글이 작성되었습니다.');
                location.reload();
            }).catch(error => {
                console.error(error);
                alert('댓글 작성에 실패했습니다.');
            });
        }

        function toggleReplyForm(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            const allReplyForms = document.querySelectorAll('.reply-form');

            // 다른 답글 폼들 숨기기
            allReplyForms.forEach(form => {
                if (form.id !== `reply-form-${commentId}`) {
                    form.classList.add('hidden');
                }
            });

            // 현재 답글 폼 토글
            replyForm.classList.toggle('hidden');

            if (!replyForm.classList.contains('hidden')) {
                const textarea = replyForm.querySelector('textarea');
                textarea.focus();
            }
        }

        function cancelReply(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.classList.add('hidden');
            replyForm.querySelector('textarea').value = '';
        }

        function submitReply(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            const textarea = replyForm.querySelector('textarea');
            const content = textarea.value.trim();

            if (!content) {
                alert('답글 내용을 입력해주세요.');
                return;
            }

            let postId = {{ $post->id }};

            axios.post('/api/post/' + postId + '/comments', {
                body: content,
                parent_id: commentId
            }).then(response => {
                alert('답글이 작성되었습니다.');
                location.reload();
            }).catch(error => {
                console.error(error);
                alert('답글 작성에 실패했습니다.');
            });
        }

        function editComment(commentId) {
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            const contentDiv = commentItem.querySelector('.comment-content');
            const editForm = commentItem.querySelector('.comment-edit-form');

            contentDiv.classList.add('hidden');
            editForm.classList.remove('hidden');

            const textarea = editForm.querySelector('textarea');
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }

        function cancelEditComment(commentId) {
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            const contentDiv = commentItem.querySelector('.comment-content');
            const editForm = commentItem.querySelector('.comment-edit-form');

            contentDiv.classList.remove('hidden');
            editForm.classList.add('hidden');
        }

        function updateComment(commentId) {
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            const editForm = commentItem.querySelector('.comment-edit-form');
            const textarea = editForm.querySelector('textarea');
            const content = textarea.value.trim();

            if (!content) {
                alert('댓글 내용을 입력해주세요.');
                return;
            }

            axios.put(`/api/comments/${commentId}`, {
                body: content,
            }).then(response => {
                alert('댓글이 수정되었습니다.');
                location.reload();
            }).catch(error => {
                console.error(error);
                alert('댓글 수정에 실패했습니다.');
            });
        }

        function deleteComment(commentId) {
            if (!confirm('정말로 이 댓글을 삭제하시겠습니까?')) {
                return;
            }

            axios.delete(`/api/comments/${commentId}`)
                .then(response => {
                    alert('댓글이 삭제되었습니다.');
                    location.reload();
                }).catch(error => {
                    console.error(error);
                    alert('댓글 삭제에 실패했습니다.');
                });
        }
    </script>
</x-app-layout>
