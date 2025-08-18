@extends('admin.layout')

@section('title', '포스트 생성')
@section('header', '새 포스트 생성')
@section('description', '새로운 포스트를 생성합니다')

@section('content')
<div id="app" class="max-w-4xl mx-auto">
    <form action="{{ route('admin.posts.store') }}" method="POST" class="space-y-6" ref="postForm">
        @csrf

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

        <!-- 이미지 첨부 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-images text-primary-500 mr-2"></i>
                이미지 첨부
            </h3>

            <!-- 드래그 드롭 업로드 영역 -->
            <div id="dropzone"
                 @click="handleDropzoneClick"
                 @dragover="handleDragOver"
                 @dragleave="handleDragLeave"
                 @drop="handleDrop"
                 class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition-colors cursor-pointer">
                <input type="file" ref="imageInput" multiple accept="image/*" class="hidden" @change="handleFileSelect">
                <div v-if="!isUploading" class="dropzone-content">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg text-gray-600 mb-2">이미지를 드래그하여 업로드하거나 클릭하세요</p>
                    <p class="text-sm text-gray-500">JPG, PNG, GIF 파일만 지원 (최대 10MB)</p>
                </div>
                <div v-if="isUploading" class="upload-progress">
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                    </div>
                    <p class="text-sm text-gray-600">업로드 중...</p>
                </div>
            </div>

            <!-- 업로드된 이미지 목록 -->
            <div v-if="showImageGallery" ref="imageGallery" class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="(image, index) in uploadedImages"
                     :key="image.id"
                     :data-image-id="image.id"
                     class="relative group image-item border border-gray-200 rounded-lg overflow-hidden">
                    <img :src="image.url" :alt="image.file_name" class="w-full h-32 object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                        <button type="button"
                                @click="deleteImage(image.id)"
                                class="opacity-0 group-hover:opacity-100 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-all duration-200">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                    <div class="absolute top-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                        <span v-text="image.sort_order"></span>
                    </div>
                    <div class="p-2 bg-white">
                        <p class="text-xs text-gray-600 truncate" v-text="image.file_name"></p>
                        <p class="text-xs text-gray-400" v-text="formatFileSize(image.file_size)"></p>
                    </div>
                </div>
            </div>

            <!-- 이미지 순서 안내 -->
            <div v-if="showImageGallery" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                이미지를 드래그하여 순서를 변경할 수 있습니다.
            </div>
        </div>

        <!-- 설정 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-cog text-primary-500 mr-2"></i>
                포스트 설정
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">타입 *</label>
                    <select id="type" name="type" v-model="form.type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('type') border-red-500 @enderror"
                            required>
                        <option value="post">포스트</option>
                        <option value="bookcase">책장</option>
                        <option value="page">페이지</option>
                        <option value="ad">광고</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                    <p class="mt-1 text-sm text-gray-500">작성자를 선택하지 않으면 시스템 포스트로 생성됩니다.</p>
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
            </div>
        </div>

        <!-- 미리보기 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-eye text-primary-500 mr-2"></i>
                미리보기
            </h3>

            <div class="prose max-w-none border border-gray-200 rounded-lg p-4 bg-gray-50 min-h-32" v-html="previewHtml">
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
                <button type="button" @click="handleSubmit('draft')"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    초안으로 저장
                </button>

                <button type="button" @click="handleSubmit('publish')"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    게시하기
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
                title: @json(old('title', '')),
                content: @json(old('content', '')),
                user_id: @json(old('user_id', '')),
                persona_id: @json(old('persona_id', '')),
                status: @json(old('status', 'draft')),
                type: @json(old('type', 'post')),
                is_ai_generated: @json(old('is_ai_generated', false))
            },
            uploadedImages: [],
            isUploading: false,
            uploadProgress: 0,
            sortable: null,
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            urls: {
                upload: @json(route('admin.posts.images.upload')),
                reorder: @json(route('admin.posts.images.reorder')),
                deleteBase: @json(url('/admin/posts/images'))
            }
        }
    },
    computed: {
        showAiSettings() {
            return this.form.is_ai_generated;
        },
        showImageGallery() {
            return this.uploadedImages.length > 0;
        },
        previewHtml() {
            let html = '';

            if (this.form.title.trim()) {
                html += `<h1 class="text-2xl font-bold text-gray-900 mb-4">${this.escapeHtml(this.form.title.trim())}</h1>`;
            }

            if (this.uploadedImages.length > 0) {
                html += '<div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-2">';
                this.uploadedImages.forEach(image => {
                    html += `<img src="${image.url}" alt="${image.file_name}" class="rounded-lg max-w-full h-auto">`;
                });
                html += '</div>';
            }

            if (this.form.content.trim()) {
                let processedContent = this.escapeHtml(this.form.content.trim());
                processedContent = processedContent.replace(/\n\n/g, '</p><p class="mb-4">');
                processedContent = processedContent.replace(/\n/g, '<br>');
                processedContent = processedContent.replace(/#(\w+)/g, '<span class="text-blue-600 font-medium">#$1</span>');
                processedContent = processedContent.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                processedContent = processedContent.replace(/\*(.*?)\*/g, '<em>$1</em>');
                html += `<div class="text-gray-700 leading-relaxed"><p class="mb-4">${processedContent}</p></div>`;
            }

            return html || '<p class="text-gray-500 italic">제목과 내용을 입력하면 미리보기가 표시됩니다.</p>';
        }
    },
    methods: {
        handleDropzoneClick(e) {
            if (e.target.closest('.dropzone-content')) {
                this.$refs.imageInput.click();
            }
        },
        handleFileSelect(e) {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                this.uploadImages(files);
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

            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                this.uploadImages(files);
            }
        },
        async uploadImages(files) {
            this.isUploading = true;
            this.uploadProgress = 0;

            const totalFiles = files.length;
            let completedUploads = 0;

            for (let file of files) {
                try {
                    await this.uploadSingleImage(file);
                    completedUploads++;
                    this.uploadProgress = (completedUploads / totalFiles) * 100;
                } catch (error) {
                    console.error('Upload error:', error);
                    this.showError('이미지 업로드 중 오류가 발생했습니다.');
                }
            }

            this.isUploading = false;
            this.$nextTick(() => {
                this.initSortable();
            });
        },
        async uploadSingleImage(file) {
            const formData = new FormData();
            formData.append('image', file);

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
                this.uploadedImages.push(data.image);
            } else {
                throw new Error(data.message);
            }
        },
        async deleteImage(imageId) {
            if (!confirm('이미지를 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`${this.urls.deleteBase}/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.uploadedImages = this.uploadedImages.filter(img => img.id != imageId);
                    this.showSuccess('이미지가 삭제되었습니다.');
                } else {
                    this.showError('이미지 삭제 실패: ' + data.message);
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showError('이미지 삭제 중 오류가 발생했습니다.');
            }
        },
        async reorderImages() {
            const imageIds = this.uploadedImages.map(img => img.id);

            try {
                const response = await fetch(this.urls.reorder, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        image_ids: imageIds
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.uploadedImages.forEach((img, index) => {
                        img.sort_order = index + 1;
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

            const gallery = this.$refs.imageGallery;
            if (gallery && this.uploadedImages.length > 0) {
                this.sortable = Sortable.create(gallery, {
                    animation: 150,
                    onEnd: (evt) => {
                        const oldIndex = evt.oldIndex;
                        const newIndex = evt.newIndex;

                        // Vue 배열 재정렬
                        const movedItem = this.uploadedImages.splice(oldIndex, 1)[0];
                        this.uploadedImages.splice(newIndex, 0, movedItem);

                        // 서버에 순서 업데이트
                        this.reorderImages();
                    }
                });
            }
        },
        formatFileSize(bytes) {
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            if (bytes === 0) return '0 Byte';
            const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        },
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        showError(message) {
            alert('오류: ' + message);
        },
        showSuccess(message) {
            alert('성공: ' + message);
        },
        handleSubmit(action) {
            if (action === 'draft') {
                this.form.status = 'draft';
            } else if (action === 'publish') {
                this.form.status = 'published';
            }

            // 임시 이미지 ID들을 hidden input에 추가
            if (this.uploadedImages.length > 0) {
                const tempImageIds = this.uploadedImages.map(img => img.id);

                // 기존 hidden input 제거
                const existingInput = this.$refs.postForm.querySelector('input[name="temp_image_ids"]');
                if (existingInput) {
                    existingInput.remove();
                }

                // 새로운 hidden input 추가
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'temp_image_ids';
                hiddenInput.value = JSON.stringify(tempImageIds);
                this.$refs.postForm.appendChild(hiddenInput);
            }

            // 폼 제출
            this.$refs.postForm.submit();
        }
    },
    mounted() {
        // 초기화
    }
}).mount('#app');
</script>
@endsection
