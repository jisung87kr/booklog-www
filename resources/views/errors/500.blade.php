@extends('layouts.app')

@section('title', '서버 오류가 발생했습니다 - BookLog')
@section('description', '일시적인 서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <!-- 500 일러스트 -->
        <div class="mb-8">
            <div class="mx-auto h-32 w-32 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-4">500</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">서버 오류가 발생했습니다</h2>
        <p class="text-gray-600 mb-8">
            일시적인 서버 문제가 발생했습니다.<br>
            잠시 후 다시 시도해주세요. 문제가 지속되면 문의해주세요.
        </p>

        <!-- 액션 버튼들 -->
        <div class="space-y-4">
            <button onclick="window.location.reload()" 
                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                새로고침
            </button>
            
            <a href="{{ route('home') }}" 
               class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                홈으로 돌아가기
            </a>
        </div>

        <!-- 문의 링크 -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-4">
                문제가 계속 발생한다면
            </p>
            <a href="{{ route('contact.create') }}" 
               class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                문의하기 →
            </a>
        </div>

        <!-- 상태 확인 링크 -->
        <div class="mt-4">
            <a href="{{ route('health.check') }}" 
               class="text-xs text-gray-400 hover:text-gray-600">
                서비스 상태 확인
            </a>
        </div>
    </div>
</div>
@endsection