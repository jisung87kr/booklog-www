@extends('admin.layout')

@section('title', '포스트 편집')
@section('header', $post->title . ' 편집')
@section('description', '포스트 내용을 수정합니다')

@section('content')
<div id="app" class="max-w-4xl mx-auto">
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            입력한 내용을 다시 확인해주세요.
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-6" ref="postForm">
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
                    <input type="text" id="title" name="title" v-model="form.title"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-500 @enderror"
                           required maxlength="255" placeholder="포스트 제목을 입력하세요">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">내용 *</label>
                    <textarea id="content" name="content" rows="10" v-model="form.content"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('content') border-red-500 @enderror"
                              required placeholder="포스트 내용을 입력하세요..."></textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">마크다운 형식을 지원합니다.</p>
                </div>
            </div>
        </div>

        <!-- 파일 첨부 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-images text-primary-500 mr-2"></i>
                파일 첨부
            </h3>

            <!-- 드래그 드롭 업로드 영역 -->
            <div id="dropzone"
                 @click="handleDropzoneClick"
                 @dragover="handleDragOver"
                 @dragleave="handleDragLeave"
                 @drop="handleDrop"
                 class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition-colors cursor-pointer">
                <input type="file" ref="fileInput" multiple class="hidden" @change="handleFileSelect" accept=".pdf,.doc,.docx,.txt,.rtf,.jpg,.jpeg,.png,.gif,.bmp,.webp,.zip,.rar,.7z,.xls,.xlsx,.csv,.ppt,.pptx,.json,.xml">
                <div v-if="!isUploading" class="dropzone-content">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg text-gray-600 mb-2">파일을 드래그하여 업로드하거나 클릭하세요</p>
                    <p class="text-sm text-gray-500">문서, 이미지, 압축파일 등 지원 (최대 10MB)</p>
                </div>
                <div v-if="isUploading" class="upload-progress">
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-sm text-gray-600">업로드 중...</p>
                </div>
            </div>

            <!-- 업로드된 파일 목록 -->
            <div v-if="showFileGallery" ref="showFileGallery" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="(file, index) in uploadedFiles"
                     :key="file.id"
                     :data-file-id="file.id"
                     class="relative group file-item border border-gray-200 rounded-lg overflow-hidden">
                    <div class="w-full h-32 bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-file text-3xl text-gray-400" v-if="!isImageFile(file.mime_type)"></i>
                        <img v-if="isImageFile(file.mime_type)" :src="file.url" :alt="file.file_name" class="w-full h-32 object-cover">
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                        <button type="button"
                                @click="deleteFile(file.id)"
                                class="opacity-0 group-hover:opacity-100 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-all duration-200">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                    <div class="absolute top-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                        <span v-text="file.sort_order"></span>
                    </div>
                    <div class="p-2 bg-white">
                        <p class="text-xs text-gray-600 truncate" v-text="file.file_name"></p>
                        <p class="text-xs text-gray-400" v-text="formatFileSize(file.file_size)"></p>
                    </div>
                </div>
            </div>

            <!-- 파일 순서 안내 -->
            <div v-if="showFileGallery" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                파일을 드래그하여 순서를 변경할 수 있습니다.
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
                    <select id="status" name="status" v-model="form.status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('status') border-red-500 @enderror"
                            required>
                        <option value="draft">초안</option>
                        <option value="published">공개됨</option>
                        <option value="unpublished">비공개됨</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="type" value="post">

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">작성자</label>
                    <select id="user_id" name="user_id" v-model="form.user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">시스템 포스트 (작성자 없음)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ '@' . ($user->username ?? $user->email) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">작성자를 선택하지 않으면 시스템 포스트로 유지됩니다.</p>
                </div>

                <div>
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="is_ai_generated" name="is_ai_generated" value="1"
                               v-model="form.is_ai_generated"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_ai_generated" class="text-sm font-medium text-gray-700">
                            AI 생성 포스트로 표시
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 ml-7">AI에 의해 생성된 포스트인 경우 체크하세요.</p>
                </div>
            </div>

            <!-- AI 관련 설정 (조건부 표시) -->
            <div v-if="showAiSettings" class="mt-6 p-4 bg-green-50 rounded-lg border border-green-200">
                <h4 class="text-sm font-semibold text-green-900 mb-3">
                    <i class="fas fa-magic text-green-600 mr-2"></i>
                    AI 생성 설정
                </h4>

                <div>
                    <label for="persona_id" class="block text-sm font-medium text-green-700 mb-2">사용할 페르소나</label>
                    <select id="persona_id" name="persona_id" v-model="form.persona_id"
                            class="w-full px-4 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                        <option value="">페르소나 없음</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}">
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

        <!-- 버튼 -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.posts') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                목록으로 돌아가기
            </a>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.posts.show', $post) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    상세보기
                </a>

                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline"
                      onsubmit="return confirm('정말로 이 포스트를 삭제하시겠습니까?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        삭제
                    </button>
                </form>

                <button type="button" @click="handleSubmit('draft')"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    초안으로 저장
                </button>

                <button type="button" @click="handleSubmit('publish')"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    변경사항 저장
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            form: {
                title: @json(old('title', $post->title)),
                content: @json(old('content', $post->content)),
                user_id: @json(old('user_id', $post->user_id)),
                persona_id: @json(old('persona_id', $personaId)),
                status: @json(old('status', $statusValue)),
                type: @json(old('type', 'post')),
                is_ai_generated: @json(old('is_ai_generated', $isAiGenerated))
            },
            uploadedFiles: [],
            isUploading: false,
            uploadProgress: 0,
            sortable: null,
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            urls: {
                upload: @json(route('admin.posts.attachments.upload')),
                deleteBase: @json(url('/admin/posts/attachments')),
                reorder: @json(route('admin.posts.attachments.reorder'))
            },
            postId: @json($post->id)
        }
    },
    computed: {
        showAiSettings() {
            return this.form.is_ai_generated;
        },
        showFileGallery() {
            return this.uploadedFiles.length > 0;
        },
    },
    methods: {
        handleDropzoneClick(e) {
            if (e.target.closest('.dropzone-content')) {
                this.$refs.fileInput.click();
            }
        },
        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                this.uploadFiles(files);
            }
        },
        handleDragOver(e) {
            e.preventDefault();
            e.target.closest('#dropzone').classList.add('border-primary-500', 'bg-primary-50');
        },
        handleDragLeave(e) {
            e.preventDefault();
            e.target.closest('#dropzone').classList.remove('border-primary-500', 'bg-primary-50');
        },
        handleDrop(e) {
            e.preventDefault();
            e.target.closest('#dropzone').classList.remove('border-primary-500', 'bg-primary-50');

            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                this.uploadFiles(files);
            }
        },
        async uploadFiles(files) {
            this.isUploading = true;
            this.uploadProgress = 0;

            const totalFiles = files.length;
            let completedUploads = 0;

            for (let file of files) {
                try {
                    await this.uploadSingleFile(file);
                    completedUploads++;
                    this.uploadProgress = (completedUploads / totalFiles) * 100;
                } catch (error) {
                    console.error('Upload error:', error);
                    this.showError('파일 업로드 중 오류가 발생했습니다.');
                }
            }

            this.isUploading = false;
            this.$nextTick(() => {
                this.initSortable();
            });
        },
        async uploadSingleFile(file) {
            const formData = new FormData();
            formData.append('file', file);

            const response = await fetch(this.urls.upload, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.uploadedFiles.push(data.file);
            } else {
                throw new Error(data.message);
            }
        },
        async deleteFile(fileId) {
            if (!confirm('파일을 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`${this.urls.deleteBase}/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.uploadedFiles = this.uploadedFiles.filter(file => file.id != fileId);
                    this.showSuccess('파일이 삭제되었습니다.');
                } else {
                    this.showError('파일 삭제 실패: ' + data.message);
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showError('파일 삭제 중 오류가 발생했습니다.');
            }
        },
        async reorderFiles() {
            const fileIds = this.uploadedFiles.map(file => file.id);

            try {
                const response = await fetch(this.urls.reorder, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        attachment_ids: fileIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.uploadedFiles.forEach((file, index) => {
                        file.sort_order = index + 1;
                    });
                } else {
                    this.showError('순서 변경 실패: ' + data.message);
                }
            } catch (error) {
                console.error('Reorder error:', error);
                this.showError('순서 변경 중 오류가 발생했습니다.');
            }
        },
        initSortable() {
            if (this.sortable) {
                this.sortable.destroy();
            }

            const gallery = this.$refs.showFileGallery;
            if (gallery && this.uploadedFiles.length > 0) {
                this.sortable = Sortable.create(gallery, {
                    animation: 150,
                    onEnd: (evt) => {
                        const oldIndex = evt.oldIndex;
                        const newIndex = evt.newIndex;

                        // Vue 배열 재정렬
                        const movedItem = this.uploadedFiles.splice(oldIndex, 1)[0];
                        this.uploadedFiles.splice(newIndex, 0, movedItem);

                        // 서버에 순서 업데이트
                        this.reorderFiles();
                    }
                });
            }
        },
        isImageFile(mimeType) {
            return mimeType && mimeType.startsWith('image/');
        },
        formatFileSize(bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            if (bytes === 0) return '0 Byte';
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        },
        showError(message) {
            alert('오류: ' + message);
        },
        showSuccess(message) {
            alert('성공: ' + message);
        },
        async loadExistingFiles() {
            try {
                const response = await fetch(`/admin/posts/${this.postId}/attachments`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    }
                });

                const data = await response.json();
                if (data.success && data.files) {
                    this.uploadedFiles = data.files;
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                }
            } catch (error) {
                console.error('Failed to load existing files:', error);
            }
        },
        handleSubmit(action) {
            if (action === 'draft') {
                this.form.status = 'draft';
            } else if (action === 'publish') {
                this.form.status = 'published';
            }

            // 폼 제출
            this.$refs.postForm.submit();
        }
    },
    mounted() {
        // 기존 첨부파일 로드
        this.loadExistingFiles();
    }
}).mount('#app');
</script>
@endsection
