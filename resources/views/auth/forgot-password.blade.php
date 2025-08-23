<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </x-slot>

        <!-- Welcome message -->
        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">비밀번호 재설정</h2>
            <p class="text-gray-600 text-sm">이메일 주소를 입력하시면 비밀번호 재설정 링크를 보내드릴게요</p>
        </div>

        <!-- Status message -->
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <span class="text-sm text-green-700">{{ session('status') }}</span>
            </div>
        @endif

        <!-- Error messages -->
        <x-validation-errors class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                <input id="email" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="이메일 주소를 입력하세요">
            </div>

            <!-- Send reset link button -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                재설정 링크 전송
            </button>
        </form>

        <!-- Back to login link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                비밀번호가 기억나셨나요? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">로그인</a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
