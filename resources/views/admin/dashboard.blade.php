@extends('admin.layout')

@section('title', '대시보드')
@section('header', '대시보드')
@section('description', '시스템 현황을 한눈에 확인하세요')

@section('content')
<!-- 통계 카드 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">전체 사용자</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100">
                <i class="fas fa-user-friends text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">페르소나</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_personas']) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100">
                <i class="fas fa-file-alt text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">전체 포스트</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_posts']) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100">
                <i class="fas fa-magic text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">AI 생성 포스트</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['ai_generated_posts']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- 빠른 작업 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-primary-500 mr-2"></i>
            빠른 작업
        </h3>
        
        <div class="space-y-3">
            <form action="{{ route('admin.generate-feeds') }}" method="POST" class="inline-block" id="generateFeedsForm">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-lg border border-green-200 transition-all duration-200 group" id="generateFeedsBtn">
                    <div class="flex items-center">
                        <i class="fas fa-magic text-green-600 mr-3" id="generateIcon"></i>
                        <div class="text-left">
                            <p class="font-medium text-green-800" id="generateTitle">AI 피드 생성</p>
                            <p class="text-sm text-green-600" id="generateDesc">모든 페르소나의 피드를 일괄 생성</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-green-600 group-hover:translate-x-1 transition-transform" id="generateArrow"></i>
                </button>
                
                <!-- 진행 상태 표시 -->
                <div id="generateProgress" class="hidden mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                        <div class="text-sm text-blue-700">
                            <span id="progressText">AI 피드를 생성하고 있습니다...</span>
                            <div class="mt-1">
                                <div class="w-full bg-blue-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: 0%" id="progressBar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <a href="{{ route('admin.personas') }}" class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-lg border border-blue-200 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-user-friends text-blue-600 mr-3"></i>
                    <div class="text-left">
                        <p class="font-medium text-blue-800">페르소나 관리</p>
                        <p class="text-sm text-blue-600">페르소나 생성 및 수정</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
            </a>
            
            <a href="{{ route('admin.users') }}" class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-lg border border-purple-200 transition-all duration-200 group">
                <div class="flex items-center">
                    <i class="fas fa-users text-purple-600 mr-3"></i>
                    <div class="text-left">
                        <p class="font-medium text-purple-800">사용자 관리</p>
                        <p class="text-sm text-purple-600">사용자 정보 및 페르소나 할당</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-purple-600 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
    
    <!-- 최근 활동 -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-clock text-primary-500 mr-2"></i>
            시스템 현황
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">AI 서비스</span>
                </div>
                <span class="text-sm font-medium text-green-600">정상</span>
            </div>
            
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">데이터베이스</span>
                </div>
                <span class="text-sm font-medium text-green-600">정상</span>
            </div>
            
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">피드 생성 비율</span>
                </div>
                <span class="text-sm font-medium text-gray-900">
                    {{ $stats['total_posts'] > 0 ? round(($stats['ai_generated_posts'] / $stats['total_posts']) * 100, 1) : 0 }}%
                </span>
            </div>
            
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                    <span class="text-sm text-gray-600">페르소나 당 평균 사용자</span>
                </div>
                <span class="text-sm font-medium text-gray-900">
                    {{ $stats['total_personas'] > 0 ? round($stats['total_users'] / $stats['total_personas'], 1) : 0 }}명
                </span>
            </div>
        </div>
    </div>
</div>

<!-- 최근 생성된 포스트 미리보기 -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-newspaper text-primary-500 mr-2"></i>
            최근 AI 생성 포스트
        </h3>
        <a href="{{ route('admin.posts') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            전체 보기 <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    @php
        $recentPosts = \App\Models\Post::whereJsonContains('meta->generated_by', 'ai')
            ->with(['user' => function($query) {
                $query->withoutGlobalScopes();
            }])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    @endphp
    
    @if($recentPosts->count() > 0)
        <div class="space-y-3">
            @foreach($recentPosts as $post)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <p class="font-medium text-gray-900 mb-1">{{ $post->title }}</p>
                    <p class="text-sm text-gray-600 mb-2">{{ Str::limit($post->content, 80) }}</p>
                    <div class="flex items-center text-xs text-gray-500">
                        <i class="fas fa-user mr-1"></i>
                        <span>{{ $post->user->name ?? 'Unknown' }}</span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-clock mr-1"></i>
                        <span>{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="ml-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-magic mr-1"></i>
                        AI 생성
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-file-alt text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500">아직 AI로 생성된 포스트가 없습니다.</p>
            <form action="{{ route('admin.generate-feeds') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-magic mr-2"></i>
                    첫 번째 AI 피드 생성하기
                </button>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('generateFeedsForm');
    const btn = document.getElementById('generateFeedsBtn');
    const progress = document.getElementById('generateProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const generateIcon = document.getElementById('generateIcon');
    const generateTitle = document.getElementById('generateTitle');
    const generateDesc = document.getElementById('generateDesc');
    const generateArrow = document.getElementById('generateArrow');

    if (form) {
        form.addEventListener('submit', function(e) {
            // 버튼 비활성화 및 스타일 변경
            btn.disabled = true;
            btn.classList.remove('hover:from-green-100', 'hover:to-green-200');
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            
            // 아이콘 변경
            generateIcon.className = 'animate-spin fas fa-spinner text-green-600 mr-3';
            generateArrow.classList.add('hidden');
            
            // 텍스트 변경
            generateTitle.textContent = '피드 생성 중...';
            generateDesc.textContent = '잠시만 기다려주세요';
            
            // 진행 상태 표시
            progress.classList.remove('hidden');
            
            // 진행 바 애니메이션
            let progress_value = 0;
            const interval = setInterval(function() {
                progress_value += Math.random() * 10;
                if (progress_value > 90) {
                    progress_value = 90;
                    clearInterval(interval);
                }
                progressBar.style.width = progress_value + '%';
            }, 500);
            
            // 진행 텍스트 업데이트
            const messages = [
                'AI 페르소나를 분석하고 있습니다...',
                '사용자 데이터를 수집하고 있습니다...',
                'AI가 개인화된 콘텐츠를 생성하고 있습니다...',
                '생성된 피드를 검토하고 있습니다...',
                '피드를 데이터베이스에 저장하고 있습니다...'
            ];
            
            let messageIndex = 0;
            const messageInterval = setInterval(function() {
                if (messageIndex < messages.length) {
                    progressText.textContent = messages[messageIndex];
                    messageIndex++;
                } else {
                    clearInterval(messageInterval);
                }
            }, 2000);
            
            // 타임아웃 처리 (30초)
            setTimeout(function() {
                if (!form.hasAttribute('data-completed')) {
                    clearInterval(interval);
                    clearInterval(messageInterval);
                    
                    // 에러 상태로 변경
                    progress.classList.remove('bg-blue-50', 'border-blue-200');
                    progress.classList.add('bg-red-50', 'border-red-200');
                    progress.querySelector('.animate-spin').classList.remove('border-blue-600');
                    progress.querySelector('.animate-spin').classList.add('border-red-600');
                    progress.querySelector('.text-blue-700').classList.remove('text-blue-700');
                    progress.querySelector('.text-blue-700').classList.add('text-red-700');
                    progressText.textContent = '처리 시간이 오래 걸리고 있습니다. 페이지를 새로고침해주세요.';
                    
                    // 버튼 원상복구
                    setTimeout(function() {
                        resetButton();
                    }, 3000);
                }
            }, 30000);
        });
    }
    
    function resetButton() {
        btn.disabled = false;
        btn.classList.remove('opacity-70', 'cursor-not-allowed');
        btn.classList.add('hover:from-green-100', 'hover:to-green-200');
        
        generateIcon.className = 'fas fa-magic text-green-600 mr-3';
        generateArrow.classList.remove('hidden');
        
        generateTitle.textContent = 'AI 피드 생성';
        generateDesc.textContent = '모든 페르소나의 피드를 일괄 생성';
        
        progress.classList.add('hidden');
        progressBar.style.width = '0%';
    }
    
    // 페이지 로드 시 성공/에러 메시지가 있다면 완료 처리
    @if(session('success') || session('error'))
        if (form) {
            form.setAttribute('data-completed', 'true');
            progressBar.style.width = '100%';
            setTimeout(function() {
                progress.classList.add('hidden');
                resetButton();
            }, 2000);
        }
    @endif
});
</script>
@endpush