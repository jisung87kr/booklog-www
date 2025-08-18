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
                <p class="text-sm font-medium text-gray-600">평균 사용자 수</p>
                <p class="text-xl font-bold text-gray-900 mt-1">
                    {{ $personas->count() > 0 ? round($personas->sum('users_count') / $personas->count(), 1) : 0 }}
                </p>
                <p class="text-sm text-gray-500">명/페르소나</p>
            </div>
            <div class="p-3 rounded-full bg-purple-100">
                <i class="fas fa-chart-line text-purple-600 text-xl"></i>
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
@endsection