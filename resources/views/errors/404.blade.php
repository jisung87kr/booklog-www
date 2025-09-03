@extends('layouts.app')

@section('title', '페이지를 찾을 수 없습니다 - BookLog')
@section('description', '요청하신 페이지를 찾을 수 없습니다. 다른 페이지를 둘러보세요.')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <!-- 404 일러스트 -->
        <div class="mb-8">
            <div class="mx-auto h-32 w-32 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">페이지를 찾을 수 없습니다</h2>
        <p class="text-gray-600 mb-8">
            요청하신 페이지가 존재하지 않거나 이동되었을 수 있습니다.<br>
            URL을 다시 확인해주세요.
        </p>

        <!-- 액션 버튼들 -->
        <div class="space-y-4">
            <a href="{{ route('home') }}" 
               class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                홈으로 돌아가기
            </a>
            
            <a href="{{ route('search.index') }}" 
               class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                검색하기
            </a>
            
            <button onclick="history.back()" 
                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                이전 페이지로
            </button>
        </div>

        <!-- 도움말 링크 -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-4">
                도움이 필요하시면 문의해주세요
            </p>
            <a href="{{ route('contact.create') }}" 
               class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                문의하기 →
            </a>
        </div>
    </div>
</div>
@endsection