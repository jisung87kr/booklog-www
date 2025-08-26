@extends('admin.layout')

@section('title', '카테고리 수정')
@section('header', '카테고리 수정')
@section('description', '카테고리 정보를 수정합니다')

@section('content')
<div id="category-edit-app">
    <div class="max-w-2xl">
        <!-- 뒤로가기 버튼 -->
        <div class="mb-6">
            <a href="{{ route('admin.categories') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                카테고리 목록으로 돌아가기
            </a>
        </div>

        <!-- 카테고리 수정 폼 -->
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" @submit="handleSubmit">
                @csrf
                @method('PUT')

                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">카테고리 정보 수정</h3>
                            <p class="mt-1 text-sm text-gray-500">카테고리 정보를 수정해주세요.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    활성
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    비활성
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-6">
                    <!-- 카테고리 타입 -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            카테고리 타입 <span class="text-red-500">*</span>
                        </label>
                        <select id="type"
                                name="type"
                                v-model="form.type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                required>
                            <option value="">타입을 선택해주세요</option>
                            @foreach(\App\Enums\CategoryTypeEnum::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">카테고리가 사용될 영역을 선택해주세요.</p>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 카테고리 이름 -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            카테고리 이름 <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               v-model="form.name"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="예: 도서, 영화, 음악"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 부모 카테고리 -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            부모 카테고리
                        </label>
                        <select id="parent_id"
                                name="parent_id"
                                v-model="form.parent_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">최상위 카테고리</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">부모 카테고리를 선택하면 하위 카테고리로 이동됩니다.</p>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 설명 -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            설명
                        </label>
                        <textarea id="description"
                                  name="description"
                                  v-model="form.description"
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="카테고리에 대한 간단한 설명을 입력해주세요"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 활성 상태 -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                   @change="handleActiveToggle"
                            >
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                카테고리 활성화
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">비활성화된 카테고리는 사용자에게 표시되지 않습니다.</p>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 카테고리 정보 -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">카테고리 정보</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">생성일</dt>
                                <dd class="text-sm text-gray-900">{{ $category->created_at->format('Y년 m월 d일 H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">마지막 수정</dt>
                                <dd class="text-sm text-gray-900">{{ $category->updated_at->format('Y년 m월 d일 H:i') }}</dd>
                            </div>
                            @if($category->child->count() > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">하위 카테고리</dt>
                                <dd class="text-sm text-gray-900">{{ $category->child->count() }}개</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- 폼 액션 -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('admin.categories') }}"
                       class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        취소
                    </a>
                    <button type="submit"
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 transition-colors">
                        <span v-if="loading">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            수정 중...
                        </span>
                        <span v-else>
                            <i class="fas fa-save mr-2"></i>
                            변경사항 저장
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: false,
            form: {
                type: '{{ $category->type ?? '' }}',
                name: '{{ $category->name }}',
                parent_id: '{{ $category->parent_id ?? '' }}',
                description: '{{ $category->description ?? '' }}',
                is_active: {{ $category->is_active ? 'true' : 'false' }}
            }
        }
    },
    methods: {
        handleSubmit(event) {
            this.loading = true;
            // 폼이 자동으로 제출되도록 함
        },
        handleActiveToggle() {
            this.form.is_active = !this.form.is_active;
        }
    }
}).mount('#category-edit-app');
</script>
@endpush
