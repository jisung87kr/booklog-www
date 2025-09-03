@extends('admin.layout')

@section('title', '문의 상세')
@section('header', '문의 상세')
@section('description', '문의 내용을 확인하고 답변할 수 있습니다.')

@section('content')
<div class="space-y-6">
    <!-- 뒤로가기 -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.contacts.index') }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-2"></i>목록으로 돌아가기
        </a>

        <div class="flex space-x-2">
            @if($contact->status === 'pending')
                <form action="{{ route('admin.contacts.update', $contact) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                        onclick="return confirm('문의를 종료하시겠습니까?')">
                        <i class="fas fa-times mr-2"></i>종료
                    </button>
                </form>
            @endif
            
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600"
                    onclick="return confirm('정말 삭제하시겠습니까?')">
                    <i class="fas fa-trash mr-2"></i>삭제
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 문의 정보 -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 문의 내용 -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">문의 내용</h3>
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
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">이름</label>
                            <p class="text-gray-900">{{ $contact->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">이메일</label>
                            <p class="text-gray-900">{{ $contact->email }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">제목</label>
                        <p class="text-gray-900 font-medium">{{ $contact->subject }}</p>
                    </div>

                    @if($contact->category)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">문의 유형</label>
                        @php
                            $categoryLabels = [
                                'general' => '일반 문의',
                                'technical' => '기술 문의', 
                                'partnership' => '제휴 문의',
                                'bug' => '버그 신고',
                                'other' => '기타'
                            ];
                        @endphp
                        <p class="text-gray-900">{{ $categoryLabels[$contact->category] ?? $contact->category }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500">접수 시간</label>
                        <p class="text-gray-900">{{ $contact->created_at->format('Y년 m월 d일 H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">문의 내용</label>
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $contact->message }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 관리자 답변 -->
            @if($contact->admin_reply)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">관리자 답변</h3>
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <p class="text-gray-900 whitespace-pre-wrap leading-relaxed">{{ $contact->admin_reply }}</p>
                    @if($contact->replied_at)
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-sm text-blue-600">답변일: {{ $contact->replied_at->format('Y년 m월 d일 H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- 답변 폼 -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">답변 작성</h3>
                
                <form action="{{ route('admin.contacts.update', $contact) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태 변경</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $contact->status === 'pending' ? 'selected' : '' }}>대기중</option>
                            <option value="replied" {{ $contact->status === 'replied' ? 'selected' : '' }}>답변완료</option>
                            <option value="closed" {{ $contact->status === 'closed' ? 'selected' : '' }}>종료</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">답변 내용</label>
                        <textarea name="admin_reply" rows="8" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="답변 내용을 입력해 주세요...">{{ $contact->admin_reply }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">답변을 작성하고 상태를 '답변완료'로 변경하면 자동으로 이메일이 발송됩니다.</p>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i>답변 저장
                    </button>
                </form>
            </div>

            <!-- 문의 메타 정보 -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">문의 정보</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">문의 ID:</span>
                        <span class="text-gray-900 font-mono">#{{ $contact->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">접수일:</span>
                        <span class="text-gray-900">{{ $contact->created_at->format('Y.m.d H:i') }}</span>
                    </div>
                    @if($contact->updated_at->ne($contact->created_at))
                    <div class="flex justify-between">
                        <span class="text-gray-500">수정일:</span>
                        <span class="text-gray-900">{{ $contact->updated_at->format('Y.m.d H:i') }}</span>
                    </div>
                    @endif
                    @if($contact->replied_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">답변일:</span>
                        <span class="text-gray-900">{{ $contact->replied_at->format('Y.m.d H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection