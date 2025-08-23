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
            <h2 class="text-xl font-semibold text-gray-900 mb-1">회원가입</h2>
            <p class="text-gray-600 text-sm">북로그와 함께 독서 여정을 시작하세요</p>
        </div>

        <!-- Error messages -->
        <x-validation-errors class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">이름</label>
                <input id="name" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="text" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       placeholder="실명을 입력하세요">
            </div>

            <!-- Username field -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">사용자명</label>
                <input id="username" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="text" 
                       name="username" 
                       value="{{ old('username') }}" 
                       required 
                       autocomplete="username"
                       placeholder="고유한 사용자명을 입력하세요">
            </div>

            <!-- Email field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                <input id="email" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autocomplete="email"
                       placeholder="이메일 주소를 입력하세요">
            </div>

            <!-- Password field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                <input id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="new-password"
                       placeholder="안전한 비밀번호를 입력하세요">
            </div>

            <!-- Password confirmation field -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">비밀번호 확인</label>
                <input id="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                       type="password" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="비밀번호를 다시 입력하세요">
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <!-- Terms checkbox -->
                <div>
                    <label for="terms" class="flex items-start cursor-pointer">
                        <input id="terms" 
                               type="checkbox" 
                               name="terms" 
                               required
                               class="w-4 h-4 mt-0.5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <div class="ml-3 text-sm">
                            <span class="text-gray-700">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-blue-600 hover:underline font-medium">서비스 이용약관</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-blue-600 hover:underline font-medium">개인정보 처리방침</a>',
                                ]) !!}
                            </span>
                        </div>
                    </label>
                </div>
            @endif

            <!-- Register button -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                계정 만들기
            </button>
        </form>

        <!-- Login link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                이미 계정이 있으신가요? 
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">로그인</a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
