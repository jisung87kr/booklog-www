@extends('admin.layout')

@section('title', '페르소나 관리')
@section('header', '페르소나 관리')
@section('description', '시스템에 등록된 페르소나를 관리합니다')

@section('content')
<!-- 상단 액션 버튼 -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-4">
        <div class="bg-white rounded-lg border border-gray-200 px-4 py-2 shadow-sm">
            <span class="text-sm text-gray-600">총 {{ $personas->count() }}개의 페르소나</span>
        </div>
    </div>
    
    <div class="flex items-center space-x-3">
        <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-download mr-2"></i>
            내보내기
        </button>
        <a href="{{ route('admin.personas.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            새 페르소나
        </a>
    </div>
</div>

<!-- 페르소나 목록 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">페르소나 목록</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">페르소나</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">기본 정보</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">독서 취향</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">자동 발행</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">사용자 수</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($personas as $persona)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr($persona->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $persona->name }}</p>
                                <p class="text-sm text-gray-500">{{ $persona->speaking_style ?? '기본 말투' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-user text-gray-400 text-xs mr-2"></i>
                                <span>{{ $persona->gender }} • {{ $persona->age }}세</span>
                            </div>
                            <div class="flex items-center mb-1">
                                <i class="fas fa-briefcase text-gray-400 text-xs mr-2"></i>
                                <span>{{ $persona->occupation }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ Str::limit($persona->description, 50) }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            @php
                                $preferences = $persona->reading_preferences ?? [];
                                $genres = $preferences['genres'] ?? [];
                                $authors = $preferences['authors'] ?? [];
                            @endphp
                            
                            @if(!empty($genres))
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach(array_slice($genres, 0, 3) as $genre)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $genre }}
                                        </span>
                                    @endforeach
                                    @if(count($genres) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            +{{ count($genres) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            @if(!empty($authors))
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-feather-alt mr-1"></i>
                                    {{ implode(', ', array_slice($authors, 0, 2)) }}
                                    @if(count($authors) > 2) 외 {{ count($authors) - 2 }}명 @endif
                                </p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            @if($persona->auto_publish_enabled)
                                <div class="flex items-center mb-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $persona->publish_frequency_label }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">{{ $persona->schedule_description }}</p>
                                @if($persona->next_publish_at)
                                    <p class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        {{ $persona->next_publish_at->format('m/d H:i') }}
                                    </p>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-pause mr-1"></i>
                                    비활성화
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $persona->users_count }}</span>
                            <span class="text-sm text-gray-500 ml-1">명</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($persona->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></div>
                                활성
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></div>
                                비활성
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.personas.edit', $persona) }}" class="text-primary-600 hover:text-primary-900 transition-colors" title="편집">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="스케줄 설정" onclick="openScheduleModal({{ $persona->id }})">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                            <form action="{{ route('admin.personas.toggle', $persona) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-{{ $persona->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $persona->is_active ? 'yellow' : 'green' }}-900 transition-colors" title="{{ $persona->is_active ? '비활성화' : '활성화' }}">
                                    <i class="fas fa-{{ $persona->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            @if($persona->users_count == 0)
                                <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" class="inline" onsubmit="return confirm('정말로 이 페르소나를 삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="삭제">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 cursor-not-allowed" title="사용 중인 페르소나는 삭제할 수 없습니다">
                                    <i class="fas fa-trash"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- 페르소나 통계 카드 -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">가장 인기있는 페르소나</p>
                @php
                    $mostPopular = $personas->sortByDesc('users_count')->first();
                @endphp
                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ $mostPopular ? $mostPopular->name : 'N/A' }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $mostPopular ? $mostPopular->users_count . '명' : '0명' }}
                </p>
            </div>
            <div class="p-3 rounded-full bg-blue-100">
                <i class="fas fa-crown text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">활성 페르소나</p>
                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ $personas->where('is_active', true)->count() }}
                </p>
                <p class="text-sm text-gray-500">
                    / {{ $personas->count() }}개
                </p>
            </div>
            <div class="p-3 rounded-full bg-green-100">
                <i class="fas fa-toggle-on text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">자동 발행 활성화</p>
                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ $personas->where('auto_publish_enabled', true)->count() }}
                </p>
                <p class="text-sm text-gray-500">/ {{ $personas->count() }}개</p>
            </div>
            <div class="p-3 rounded-full bg-purple-100">
                <i class="fas fa-robot text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- 빠른 작업 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-lightning-bolt text-primary-500 mr-2"></i>
        빠른 작업
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <form action="{{ route('admin.generate-feeds') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center p-4 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-lg border border-green-200 transition-all duration-200 group">
                <i class="fas fa-magic text-green-600 mr-2"></i>
                <span class="text-green-800 font-medium">전체 피드 생성</span>
            </button>
        </form>
        
        <a href="{{ route('admin.personas.create') }}" class="w-full flex items-center justify-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-lg border border-blue-200 transition-all duration-200 group">
            <i class="fas fa-plus text-blue-600 mr-2"></i>
            <span class="text-blue-800 font-medium">새 페르소나</span>
        </a>
        
        <button class="w-full flex items-center justify-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-lg border border-purple-200 transition-all duration-200 group">
            <i class="fas fa-copy text-purple-600 mr-2"></i>
            <span class="text-purple-800 font-medium">페르소나 복제</span>
        </button>
        
        <button class="w-full flex items-center justify-center p-4 bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 rounded-lg border border-yellow-200 transition-all duration-200 group">
            <i class="fas fa-file-export text-yellow-600 mr-2"></i>
            <span class="text-yellow-800 font-medium">데이터 내보내기</span>
        </button>
    </div>
</div>

<!-- 스케줄 설정 모달 -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">자동 발행 스케줄 설정</h3>
                    <button type="button" onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="scheduleForm" action="{{ route('admin.personas.schedule') }}" method="POST">
                    @csrf
                    <input type="hidden" id="persona_id" name="persona_id">
                    
                    <!-- 자동 발행 활성화 -->
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="hidden" name="auto_publish_enabled" value="0">
                            <input type="checkbox" id="auto_publish_enabled" name="auto_publish_enabled" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">자동 발행 활성화</span>
                        </label>
                    </div>
                    
                    <!-- 발행 주기 -->
                    <div class="mb-4" id="frequencyGroup">
                        <label class="block text-sm font-medium text-gray-700 mb-2">발행 주기</label>
                        <select id="publish_frequency" name="publish_frequency" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">선택하세요</option>
                            <option value="hourly">매시간</option>
                            <option value="daily">매일</option>
                            <option value="weekly">매주</option>
                        </select>
                    </div>
                    
                    <!-- 매일 설정 -->
                    <div class="mb-4 hidden" id="dailySchedule">
                        <label class="block text-sm font-medium text-gray-700 mb-2">발행 시간</label>
                        <div class="flex space-x-2">
                            <select id="daily_hour" name="daily_hour" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @for($i = 0; $i < 24; $i++)
                                    <option value="{{ $i }}" {{ $i == 9 ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}시</option>
                                @endfor
                            </select>
                            <select id="daily_minute" name="daily_minute" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                @for($i = 0; $i < 60; $i += 15)
                                    <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}분</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <!-- 매주 설정 -->
                    <div class="mb-4 hidden" id="weeklySchedule">
                        <label class="block text-sm font-medium text-gray-700 mb-2">요일 및 시간</label>
                        <div class="space-y-2">
                            <select id="weekly_day" name="weekly_day" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="0">일요일</option>
                                <option value="1" selected>월요일</option>
                                <option value="2">화요일</option>
                                <option value="3">수요일</option>
                                <option value="4">목요일</option>
                                <option value="5">금요일</option>
                                <option value="6">토요일</option>
                            </select>
                            <div class="flex space-x-2">
                                <select id="weekly_hour" name="weekly_hour" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    @for($i = 0; $i < 24; $i++)
                                        <option value="{{ $i }}" {{ $i == 9 ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}시</option>
                                    @endfor
                                </select>
                                <select id="weekly_minute" name="weekly_minute" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    @for($i = 0; $i < 60; $i += 15)
                                        <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}분</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="closeScheduleModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            취소
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                            저장
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const personas = @json($personas->keyBy('id'));

function openScheduleModal(personaId) {
    const persona = personas[personaId];
    const modal = document.getElementById('scheduleModal');
    const form = document.getElementById('scheduleForm');
    
    // 디버깅을 위한 로그
    console.log('모달 열기 - 페르소나 데이터:', persona);
    
    // 폼 초기화
    form.reset();
    document.getElementById('persona_id').value = personaId;
    
    // 현재 설정 로드
    if (persona.auto_publish_enabled) {
        document.getElementById('auto_publish_enabled').checked = true;
        document.getElementById('publish_frequency').value = persona.publish_frequency || '';
        
        const schedule = persona.publish_schedule || {};
        
        if (persona.publish_frequency === 'daily') {
            document.getElementById('daily_hour').value = schedule.hour || 9;
            document.getElementById('daily_minute').value = schedule.minute || 0;
        } else if (persona.publish_frequency === 'weekly') {
            document.getElementById('weekly_day').value = schedule.day_of_week || 1;
            document.getElementById('weekly_hour').value = schedule.hour || 9;
            document.getElementById('weekly_minute').value = schedule.minute || 0;
        }
    }
    
    updateScheduleFields();
    modal.classList.remove('hidden');
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').classList.add('hidden');
}

function updateScheduleFields() {
    const enabled = document.getElementById('auto_publish_enabled').checked;
    const frequency = document.getElementById('publish_frequency').value;
    const frequencyGroup = document.getElementById('frequencyGroup');
    const dailySchedule = document.getElementById('dailySchedule');
    const weeklySchedule = document.getElementById('weeklySchedule');
    
    // 활성화 상태에 따라 표시/숨김
    frequencyGroup.style.display = enabled ? 'block' : 'none';
    
    // 주기에 따른 세부 설정 표시/숨김
    dailySchedule.classList.add('hidden');
    weeklySchedule.classList.add('hidden');
    
    if (enabled && frequency === 'daily') {
        dailySchedule.classList.remove('hidden');
    } else if (enabled && frequency === 'weekly') {
        weeklySchedule.classList.remove('hidden');
    }
}

// 이벤트 리스너
document.getElementById('auto_publish_enabled').addEventListener('change', updateScheduleFields);
document.getElementById('publish_frequency').addEventListener('change', updateScheduleFields);

// 폼 제출 시 디버깅
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    const formData = new FormData(this);
    console.log('폼 제출 데이터:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
});

// 모달 외부 클릭 시 닫기
document.getElementById('scheduleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeScheduleModal();
    }
});
</script>
@endpush