@extends('admin.layout')

@section('title', '카테고리 관리')
@section('header', '카테고리 관리')
@section('description', '카테고리를 생성, 수정, 삭제할 수 있습니다')

@section('content')
<div id="category-app">
    <!-- 헤더 액션 -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="flex items-center space-x-2 mb-4 sm:mb-0">
            <h3 class="text-lg font-medium text-gray-900">전체 카테고리</h3>
            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-sm">{{ count($categories) }}개</span>
        </div>

        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                새 카테고리 추가
            </a>
        </div>
    </div>

    <!-- 카테고리 테이블 -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">카테고리</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">타입</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">부모 카테고리</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">설명</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">생성일</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($category->parent_id)
                                    <i class="fas fa-level-up-alt text-gray-400 mr-2 transform rotate-90"></i>
                                @endif
                                <div>
                                    <div class="flex items-center">
                                        <i class="fas fa-tag text-gray-400 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ \App\Enums\CategoryTypeEnum::tryFrom($category->type)?->label() ?? $category->type }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->parent)
                                <span class="text-sm text-gray-600">{{ $category->parent->name }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">
                                {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button @click="toggleStatus({{ $category->id }}, {{ $category->is_active ? 'true' : 'false' }})"
                                    :disabled="loading"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors cursor-pointer"
                                    :class="'{{ $category->is_active ? "bg-green-100 text-green-800 hover:bg-green-200" : "bg-red-100 text-red-800 hover:bg-red-200" }}'">
                                @if($category->is_active)
                                    <i class="fas fa-check-circle mr-1"></i>
                                    활성
                                @else
                                    <i class="fas fa-times-circle mr-1"></i>
                                    비활성
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="text-primary-600 hover:text-primary-900 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button @click="deleteCategory({{ $category->id }}, '{{ $category->name }}')"
                                        :disabled="loading"
                                        class="text-red-600 hover:text-red-900 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-tags text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">카테고리가 없습니다</h3>
                                <p class="text-sm text-gray-500 mb-4">첫 번째 카테고리를 만들어보세요.</p>
                                <a href="{{ route('admin.categories.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    카테고리 추가
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 삭제 확인 모달 -->
    <template v-show="showDeleteModal">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeDeleteModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">카테고리 삭제</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">
                                "<strong>@{{ deleteTarget.name }}</strong>" 카테고리를 삭제하시겠습니까?<br>
                                이 작업은 되돌릴 수 없습니다.
                            </p>
                        </div>
                        <div class="flex items-center justify-center space-x-4 mt-4">
                            <button @click="closeDeleteModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                                취소
                            </button>
                            <button @click="confirmDelete"
                                    :disabled="loading"
                                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50">
                                <span v-if="loading">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    삭제 중...
                                </span>
                                <span v-else>삭제</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: false,
            showDeleteModal: false,
            deleteTarget: {
                id: null,
                name: ''
            }
        }
    },
    mounted() {
        // CSRF 토큰 설정
        //axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    },
    methods: {
        async toggleStatus(categoryId, currentStatus) {
            this.loading = true;

            try {
                const response = await axios.post(`/admin/categories/${categoryId}/toggle`);

                if (response.data.success || response.status === 200) {
                    // 페이지 새로고침으로 상태 업데이트
                    window.location.reload();
                } else {
                    throw new Error('상태 변경에 실패했습니다.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('상태 변경 중 오류가 발생했습니다.');
            } finally {
                this.loading = false;
            }
        },

        deleteCategory(categoryId, categoryName) {
            this.deleteTarget = {
                id: categoryId,
                name: categoryName
            };
            this.showDeleteModal = true;
        },

        closeDeleteModal() {
            this.showDeleteModal = false;
            this.deleteTarget = {
                id: null,
                name: ''
            };
        },

        async confirmDelete() {
            this.loading = true;
            // ajax 요청이 아닌 폼 제출로 변경
            let actionUrl = `/admin/categories/${this.deleteTarget.id}`;
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = actionUrl;
            let csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            let methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();


            // try {
            //     const response = await axios.delete(`/admin/categories/${this.deleteTarget.id}`);
            //
            //     if (response.data.success || response.status === 200) {
            //         // 성공 시 페이지 새로고침
            //         // window.location.reload();
            //     } else {
            //         throw new Error('카테고리 삭제에 실패했습니다.');
            //     }
            // } catch (error) {
            //     console.error('Error:', error);
            //
            //     if (error.response && error.response.data && error.response.data.message) {
            //         alert(error.response.data.message);
            //     } else {
            //         alert('카테고리 삭제 중 오류가 발생했습니다.');
            //     }
            // } finally {
            //     this.loading = false;
            //     this.closeDeleteModal();
            // }
        }
    }
}).mount('#category-app');
</script>
@endpush
