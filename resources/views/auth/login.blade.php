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
            <h2 class="text-xl font-semibold text-gray-900 mb-1">로그인</h2>
            <p class="text-gray-600 text-sm">북로그 계정으로 로그인하세요</p>
        </div>

        <!-- Error messages -->
        <x-validation-errors class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" />

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <span class="text-sm text-green-700">{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Username field -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">사용자명</label>
                <input id="username" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="text" 
                       name="username" 
                       value="{{ old('username') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="사용자명을 입력하세요">
            </div>

            <!-- Password field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                <input id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="비밀번호를 입력하세요">
            </div>

            <!-- Remember me and forgot password -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                    <span class="ml-2 text-sm text-gray-700">로그인 상태 유지</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        비밀번호를 잊으셨나요?
                    </a>
                @endif
            </div>

            <!-- Login button -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                로그인
            </button>
        </form>

        <!-- Register link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                아직 계정이 없으신가요? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">회원가입</a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>