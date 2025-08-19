@extends('admin.layout')

@section('title', '사용자 편집')
@section('header', $user->name . ' 편집')
@section('description', '사용자 정보를 수정합니다')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- 기본 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-primary-500 mr-2"></i>
                기본 정보
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름 *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일 *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">사용자명</label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('username') border-red-500 @enderror"
                           placeholder="@username">
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-2">할당된 페르소나</label>
                    <select id="persona_id" name="persona_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">페르소나 없음</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}" {{ old('persona_id', $user->persona_id) == $persona->id ? 'selected' : '' }}>
                                {{ $persona->name }} ({{ $persona->occupation }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <!-- 보안 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-lock text-primary-500 mr-2"></i>
                보안 정보
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">새 비밀번호</label>
                    <input type="password" id="password" name="password" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                           placeholder="새 비밀번호 (변경하지 않으려면 비워두세요)">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">최소 8자 이상, 영문/숫자/특수문자 조합</p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">비밀번호 확인</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="새 비밀번호 확인">
                    <p class="mt-1 text-xs text-gray-500">위와 동일한 비밀번호를 입력하세요</p>
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5 mr-3"></i>
                    <div class="text-sm text-amber-800">
                        <p class="font-medium mb-1">비밀번호 변경 시 주의사항</p>
                        <ul class="text-xs space-y-1 text-amber-700">
                            <li>• 비밀번호를 변경하면 사용자가 다시 로그인해야 합니다</li>
                            <li>• 비밀번호를 변경하지 않으려면 두 필드 모두 비워두세요</li>
                            <li>• 강력한 비밀번호를 사용하는 것이 권장됩니다</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 계정 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-primary-500 mr-2"></i>
                계정 정보
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">가입일</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $user->created_at->format('Y년 m월 d일') }}
                        <span class="text-sm text-gray-500">({{ $user->created_at->diffForHumans() }})</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">최근 활동</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-700">
                        {{ $user->updated_at->format('Y년 m월 d일') }}
                        <span class="text-sm text-gray-500">({{ $user->updated_at->diffForHumans() }})</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">이메일 인증</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                인증 완료 ({{ $user->email_verified_at->format('Y-m-d') }})
                            </span>
                        @else
                            <span class="inline-flex items-center text-red-700">
                                <i class="fas fa-times-circle mr-2"></i>
                                미인증
                            </span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">프로필 사진</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                        @if($user->profile_photo_path)
                            <span class="inline-flex items-center text-blue-700">
                                <i class="fas fa-image mr-2"></i>
                                설정됨
                            </span>
                        @else
                            <span class="inline-flex items-center text-gray-500">
                                <i class="fas fa-user-circle mr-2"></i>
                                기본 이미지
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 활동 통계 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-primary-500 mr-2"></i>
                활동 통계
            </h3>
            
            @php
                $postsCount = $user->posts()->count();
                $aiPostsCount = $user->posts()->whereJsonContains('meta->generated_by', 'ai')->count();
                $commentsCount = $user->comments()->count();
                $followersCount = \Illuminate\Support\Facades\DB::table('follows')->where('following_id', $user->id)->count();
                $followingCount = \Illuminate\Support\Facades\DB::table('follows')->where('follow_id', $user->id)->count();
                $bookcasesCount = $user->bookcases()->count();
            @endphp
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $postsCount }}</div>
                    <div class="text-xs text-blue-600">전체 포스트</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $aiPostsCount }}</div>
                    <div class="text-xs text-green-600">AI 생성</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $commentsCount }}</div>
                    <div class="text-xs text-purple-600">댓글</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $followersCount }}</div>
                    <div class="text-xs text-yellow-600">팔로워</div>
                </div>
                <div class="text-center p-4 bg-pink-50 rounded-lg">
                    <div class="text-2xl font-bold text-pink-600">{{ $followingCount }}</div>
                    <div class="text-xs text-pink-600">팔로잉</div>
                </div>
                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                    <div class="text-2xl font-bold text-indigo-600">{{ $bookcasesCount }}</div>
                    <div class="text-xs text-indigo-600">서재</div>
                </div>
            </div>
        </div>
        
        <!-- 최근 활동 -->
        @if($user->posts()->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-clock text-primary-500 mr-2"></i>
                최근 포스트
            </h3>
            
            <div class="space-y-3">
                @foreach($user->posts()->latest()->limit(5)->get() as $post)
                    <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div>
                            @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
                                <i class="fas fa-magic text-green-600"></i>
                            @else
                                <i class="fas fa-edit text-blue-600"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $post->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Str::limit(strip_tags($post->content), 80) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $post->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if(isset($post->meta['generated_by']) && $post->meta['generated_by'] === 'ai')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    AI
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    사용자
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- 버튼 -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                목록으로 돌아가기
            </a>
            
            <div class="flex items-center space-x-3">
                <!-- AI 피드 생성 -->
                @if($user->persona_id)
                    <form action="{{ route('admin.users.generate-feed', $user) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-magic mr-2"></i>
                            AI 피드 생성
                        </button>
                    </form>
                @endif
                
                <!-- 페르소나 해제 -->
                @if($user->persona_id)
                    <form action="{{ route('admin.users.remove-persona', $user) }}" method="POST" class="inline"
                          onsubmit="return confirm('이 사용자의 페르소나 할당을 해제하시겠습니까?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-user-times mr-2"></i>
                            페르소나 해제
                        </button>
                    </form>
                @endif
                
                <!-- 사용자 삭제 -->
                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                          onsubmit="return confirm('정말로 이 사용자를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            사용자 삭제
                        </button>
                    </form>
                @endif
                
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    변경사항 저장
                </button>
            </div>
        </div>
    </form>
</div>
@endsection