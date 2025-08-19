@extends('admin.layout')

@section('title', '사용자 관리')
@section('header', '사용자 관리')
@section('description', '시스템에 등록된 사용자를 관리합니다')

@section('content')
<!-- 상단 통계 및 액션 -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <i class="fas fa-users text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">전체 사용자</p>
                <p class="text-xl font-bold text-gray-900">{{ $users->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <i class="fas fa-user-check text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">페르소나 할당</p>
                <p class="text-xl font-bold text-gray-900">{{ $users->where('persona_id', '!=', null)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
                <i class="fas fa-user-times text-yellow-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">미할당</p>
                <p class="text-xl font-bold text-gray-900">{{ $users->where('persona_id', null)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
                <i class="fas fa-magic text-purple-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-600">AI 피드 생성 대상</p>
                <p class="text-xl font-bold text-gray-900">{{ $users->where('persona_id', '!=', null)->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- 필터 및 검색 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="사용자 검색..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option>모든 페르소나</option>
                <option>페르소나 할당됨</option>
                <option>페르소나 미할당</option>
                @foreach(\App\Models\Persona::all() as $persona)
                    <option value="{{ $persona->id }}">{{ $persona->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-download mr-2"></i>
                내보내기
            </button>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-user-plus mr-2"></i>
                새 사용자
            </a>
        </div>
    </div>
</div>

<!-- 사용자 목록 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">사용자 목록</h3>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>{{ $users->firstItem() }} - {{ $users->lastItem() }}</span>
                <span>/</span>
                <span>{{ $users->total() }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">사용자</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">페르소나</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">가입일</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">활동</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->username ?? 'username' }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->persona)
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-2">
                                    {{ substr($user->persona->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->persona->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->persona->occupation }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">페르소나 미할당</p>
                                    <button class="text-xs text-primary-600 hover:text-primary-700 persona-assign-btn" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">할당하기</button>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>
                            <p>{{ $user->created_at->format('Y-m-d') }}</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                            @php
                                $postCount = $user->posts()->count();
                                $aiPostCount = $user->posts()->whereJsonContains('meta->generated_by', 'ai')->count();
                            @endphp
                            <p class="text-gray-900">포스트 {{ $postCount }}개</p>
                            @if($aiPostCount > 0)
                                <p class="text-xs text-green-600">
                                    <i class="fas fa-magic mr-1"></i>
                                    AI 생성 {{ $aiPostCount }}개
                                </p>
                            @endif
                            <p class="text-xs text-gray-500">
                                최근 로그인: {{ $user->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 hover:text-primary-900 transition-colors" title="편집">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->persona)
                                <form action="{{ route('admin.users.generate-feed', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="AI 피드 생성">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </form>
                            @else
                                <button class="text-blue-600 hover:text-blue-900 transition-colors persona-assign-btn"
                                        title="페르소나 할당"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            @endif
                            <a href="{{ route('profile', $user->username ?? $user->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 transition-colors" title="사용자 페이지 보기">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>

<!-- 대량 작업 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-tasks text-primary-500 mr-2"></i>
        대량 작업
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-blue-800">페르소나 일괄 할당</h4>
                <i class="fas fa-user-friends text-blue-600"></i>
            </div>
            <p class="text-sm text-blue-600 mb-3">선택된 사용자에게 페르소나를 일괄 할당합니다.</p>
            <button class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                시작하기
            </button>
        </div>

        <form action="{{ route('admin.generate-feeds') }}" method="POST">
            @csrf
            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 h-full">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-green-800">AI 피드 생성</h4>
                    <i class="fas fa-magic text-green-600"></i>
                </div>
                <p class="text-sm text-green-600 mb-3">페르소나가 할당된 모든 사용자의 피드를 생성합니다.</p>
                <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded transition-colors">
                    생성하기
                </button>
            </div>
        </form>

        <div class="p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg border border-yellow-200">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-yellow-800">데이터 내보내기</h4>
                <i class="fas fa-download text-yellow-600"></i>
            </div>
            <p class="text-sm text-yellow-600 mb-3">사용자 데이터를 CSV 또는 Excel로 내보냅니다.</p>
            <button class="w-full px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded transition-colors">
                내보내기
            </button>
        </div>

        <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-purple-800">통계 리포트</h4>
                <i class="fas fa-chart-bar text-purple-600"></i>
            </div>
            <p class="text-sm text-purple-600 mb-3">사용자 활동 통계 및 분석 리포트를 생성합니다.</p>
            <button class="w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded transition-colors">
                생성하기
            </button>
        </div>
    </div>
</div>

<!-- 페르소나 할당 모달 -->
<div id="persona-assign-app">
    <template v-if="showModal" >
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">페르소나 할당</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">사용자: <span class="font-medium">@{{ selectedUser.name }}</span></p>
                    </div>

                    <form @submit.prevent="assignPersona">
                        <div class="mb-4">
                            <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-2">페르소나 선택</label>
                            <select v-model="selectedPersonaId" id="persona_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">페르소나를 선택하세요</option>
                                @foreach(\App\Models\Persona::where('is_active', true)->get() as $persona)
                                    <option value="{{ $persona->id }}">
                                        {{ $persona->name }} ({{ $persona->occupation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                취소
                            </button>
                            <button type="submit" :disabled="!selectedPersonaId || loading" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-50">
                                <span v-if="loading">할당 중...</span>
                                <span v-else>할당하기</span>
                            </button>
                        </div>
                    </form>
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
            showModal: false,
            selectedUser: {
                id: null,
                name: ''
            },
            selectedPersonaId: '',
            loading: false
        }
    },
    mounted() {
        // 페르소나 할당 버튼 클릭 이벤트
        document.querySelectorAll('.persona-assign-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const target = e.target.closest('button');
                this.selectedUser.id = target.getAttribute('data-user-id');
                this.selectedUser.name = target.getAttribute('data-user-name');
                this.showModal = true;
            });
        });
    },
    methods: {
        closeModal() {
            this.showModal = false;
            this.selectedPersonaId = '';
            this.selectedUser = { id: null, name: '' };
            this.loading = false;
        },
        async assignPersona() {
            this.loading = true;
            try {
                const response = await axios.post(`/admin/users/${this.selectedUser.id}/assign-persona`, {
                    persona_id: this.selectedPersonaId
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.data.success) {
                    this.closeModal();
                    window.location.reload();
                } else {
                    alert(response.data.message || '페르소나 할당에 실패했습니다.');
                }
            } catch (error) {
                console.error('Error:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    alert(error.response.data.message);
                } else {
                    alert('페르소나 할당 중 오류가 발생했습니다.');
                }
            } finally {
                this.loading = false;
            }
        }
    }
}).mount('#persona-assign-app');
</script>
@endpush
