@extends('admin.layout')

@section('title', '문의 관리')
@section('header', '문의 관리')
@section('description', '사용자 문의를 관리하고 답변할 수 있습니다.')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- 필터 -->
    <div class="p-6 border-b border-gray-200">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">검색</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="이름, 이메일, 제목으로 검색"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">상태</label>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">전체</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>대기중</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>답변완료</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>종료</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">문의유형</label>
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">전체</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>검색
                </button>
                <a href="{{ route('admin.contacts.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-refresh mr-2"></i>초기화
                </a>
            </div>
        </form>
    </div>

    <!-- 통계 -->
    <div class="p-6 bg-gray-50 border-b">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm font-medium text-gray-500">전체 문의</div>
                <div class="text-2xl font-bold text-gray-900">{{ $contacts->total() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm font-medium text-gray-500">대기중</div>
                <div class="text-2xl font-bold text-orange-600">{{ $contacts->where('status', 'pending')->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm font-medium text-gray-500">답변완료</div>
                <div class="text-2xl font-bold text-green-600">{{ $contacts->where('status', 'replied')->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="text-sm font-medium text-gray-500">종료</div>
                <div class="text-2xl font-bold text-gray-600">{{ $contacts->where('status', 'closed')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- 문의 목록 -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">문의자</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">제목</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">유형</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">접수일</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($contact->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-clock mr-1"></i>대기중
                            </span>
                        @elseif($contact->status === 'replied')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>답변완료
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times mr-1"></i>종료
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                        <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 max-w-xs truncate">{{ $contact->subject }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($contact->category)
                            @php
                                $categoryLabels = [
                                    'general' => '일반',
                                    'technical' => '기술', 
                                    'partnership' => '제휴',
                                    'bug' => '버그',
                                    'other' => '기타'
                                ];
                            @endphp
                            <span class="text-sm text-gray-600">{{ $categoryLabels[$contact->category] ?? $contact->category }}</span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $contact->created_at->format('Y.m.d H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.contacts.show', $contact) }}" 
                                class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye mr-1"></i>보기
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('정말 삭제하시겠습니까?')">
                                    <i class="fas fa-trash mr-1"></i>삭제
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                        <div>등록된 문의가 없습니다.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- 페이지네이션 -->
    @if($contacts->hasPages())
    <div class="px-6 py-3 border-t border-gray-200">
        {{ $contacts->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection