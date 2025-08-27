@extends('admin.layout')

@section('title', '포스트 편집')
@section('header', $post->title . ' 편집')
@section('description', '포스트 내용을 수정합니다')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.feeds.update', $post) }}" method="POST" class="space-y-6" id="postForm">
        @csrf
        @method('PUT')

        <!-- 기본 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-edit text-primary-500 mr-2"></i>
                기본 정보
            </h3>

            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                           required maxlength="255" placeholder="포스트 제목을 입력하세요">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">내용 *</label>
                    <textarea id="content" name="content" rows="12"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('content') border-red-500 @enderror"
                              required placeholder="포스트 내용을 입력하세요...">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">마크다운 형식을 지원합니다.</p>
                </div>
            </div>
        </div>

        <!-- 설정 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-cog text-primary-500 mr-2"></i>
                포스트 설정
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $statusValue = is_object($post->status) ? $post->status->value : $post->status;
                    $typeValue = is_object($post->type) ? $post->type->value : $post->type;
                    $isAiGenerated = isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai';
                    $personaId = $post->meta['persona_id'] ?? null;
                @endphp

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">상태 *</label>
                    <select id="status" name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="draft" {{ old('status', $statusValue) === 'draft' ? 'selected' : '' }}>초안</option>
                        <option value="published" {{ old('status', $statusValue) === 'published' ? 'selected' : '' }}>공개됨</option>
                        <option value="unpublished" {{ old('status', $statusValue) === 'unpublished' ? 'selected' : '' }}>비공개됨</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">타입 *</label>
                    <select id="type" name="type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('type') border-red-500 @enderror"
                            required>
                        <option value="post" {{ old('type', $typeValue) === 'post' ? 'selected' : '' }}>포스트</option>
                        <option value="bookcase" {{ old('type', $typeValue) === 'bookcase' ? 'selected' : '' }}>책장</option>
                        <option value="page" {{ old('type', $typeValue) === 'page' ? 'selected' : '' }}>페이지</option>
                        <option value="ad" {{ old('type', $typeValue) === 'ad' ? 'selected' : '' }}>광고</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">작성자</label>
                    <select id="user_id" name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">시스템 포스트 (작성자 없음)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $post->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->username ?? $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">작성자를 선택하지 않으면 시스템 포스트로 유지됩니다.</p>
                </div>

                <div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="is_ai_generated" name="is_ai_generated" value="1"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                               {{ old('is_ai_generated', $isAiGenerated) ? 'checked' : '' }}>
                        <label for="is_ai_generated" class="text-sm font-medium text-gray-700">
                            AI 생성 포스트로 표시
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 ml-7">AI에 의해 생성된 포스트인 경우 체크하세요.</p>
                </div>
            </div>

            <!-- AI 관련 설정 (조건부 표시) -->
            <div id="ai-settings" class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200" style="display: {{ $isAiGenerated ? 'block' : 'none' }};">
                <h4 class="text-sm font-semibold text-green-900 mb-3">
                    <i class="fas fa-magic text-green-600 mr-2"></i>
                    AI 생성 설정
                </h4>

                <div>
                    <label for="persona_id" class="block text-sm font-medium text-green-700 mb-2">사용할 페르소나</label>
                    <select id="persona_id" name="persona_id"
                            class="w-full px-4 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                        <option value="">페르소나 없음</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}" {{ old('persona_id', $personaId) == $persona->id ? 'selected' : '' }}>
                                {{ $persona->name }} ({{ $persona->occupation }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-green-600">AI 포스트를 어떤 페르소나가 작성했는지 기록합니다.</p>
                </div>

                @if(isset($post->meta['generated_at']))
                    <div class="mt-3 text-xs text-green-600">
                        <i class="fas fa-clock mr-1"></i>
                        AI 생성 시간: {{ \Carbon\Carbon::parse($post->meta['generated_at'])->format('Y-m-d H:i:s') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- 포스트 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-primary-500 mr-2"></i>
                포스트 정보
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">생성일</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $post->created_at->format('Y년 m월 d일 H:i') }}
                        <div class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">수정일</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $post->updated_at->format('Y년 m월 d일 H:i') }}
                        <div class="text-xs text-gray-500">{{ $post->updated_at->diffForHumans() }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">게시일</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        @if($post->published_at)
                            {{ $post->published_at->format('Y년 m월 d일 H:i') }}
                            <div class="text-xs text-gray-500">{{ $post->published_at->diffForHumans() }}</div>
                        @else
                            <span class="text-gray-500 italic">미게시</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 미리보기 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-eye text-primary-500 mr-2"></i>
                미리보기
            </h3>

            <div id="preview" class="prose max-w-none border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-32">
                <!-- JavaScript로 채워질 내용 -->
            </div>
        </div>

        <!-- 버튼 -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.feeds') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                목록으로 돌아가기
            </a>

            <div class="flex items-center space-x-3">
                <button type="button" id="preview-btn"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    미리보기 새로고침
                </button>

                <a href="{{ route('admin.feeds.show', $post) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    상세보기
                </a>

                <form action="{{ route('admin.feeds.destroy', $post) }}" method="POST" class="inline"
                      onsubmit="return confirm('정말로 이 포스트를 삭제하시겠습니까?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        삭제
                    </button>
                </form>

                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    변경사항 저장
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isAiCheckbox = document.getElementById('is_ai_generated');
    const aiSettings = document.getElementById('ai-settings');
    const titleInput = document.getElementById('title');
    const contentTextarea = document.getElementById('content');
    const preview = document.getElementById('preview');
    const previewBtn = document.getElementById('preview-btn');

    // AI 설정 토글
    function toggleAiSettings() {
        if (isAiCheckbox.checked) {
            aiSettings.style.display = 'block';
        } else {
            aiSettings.style.display = 'none';
        }
    }

    isAiCheckbox.addEventListener('change', toggleAiSettings);

    // 미리보기 업데이트
    function updatePreview() {
        const title = titleInput.value.trim();
        const content = contentTextarea.value.trim();

        if (!title && !content) {
            preview.innerHTML = '<p class="text-gray-500 italic">제목과 내용을 입력하면 미리보기가 표시됩니다.</p>';
            return;
        }

        let previewHtml = '';

        if (title) {
            previewHtml += `<h1 class="text-2xl font-bold text-gray-900 mb-4">${escapeHtml(title)}</h1>`;
        }

        if (content) {
            // 간단한 마크다운 렌더링
            let processedContent = escapeHtml(content);

            // 줄바꿈 처리
            processedContent = processedContent.replace(/\n\n/g, '</p><p class="mb-4">');
            processedContent = processedContent.replace(/\n/g, '<br>');

            // 해시태그 처리
            processedContent = processedContent.replace(/#(\w+)/g, '<span class="text-blue-600 font-medium">#$1</span>');

            // 볼드 텍스트 처리 (**text**)
            processedContent = processedContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // 이탤릭 텍스트 처리 (*text*)
            processedContent = processedContent.replace(/\*(.*?)\*/g, '<em>$1</em>');

            previewHtml += `<div class="text-gray-700 leading-relaxed"><p class="mb-4">${processedContent}</p></div>`;
        }

        preview.innerHTML = previewHtml;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // 실시간 미리보기
    titleInput.addEventListener('input', updatePreview);
    contentTextarea.addEventListener('input', updatePreview);
    previewBtn.addEventListener('click', updatePreview);

    // 초기 미리보기
    updatePreview();
});
</script>
@endsection
