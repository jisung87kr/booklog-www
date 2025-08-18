@extends('admin.layout')

@section('title', '새 사용자 생성')
@section('header', '새 사용자 생성')
@section('description', '새로운 사용자 계정을 생성합니다')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- 기본 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-primary-500 mr-2"></i>
                기본 정보
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름 *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           placeholder="사용자의 실명을 입력하세요" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일 *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                           placeholder="example@email.com" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">사용자명</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('username') border-red-500 @enderror"
                           placeholder="username (영문, 숫자, 언더스코어만 가능)">
                    <p class="mt-1 text-xs text-gray-500">비어두면 자동으로 생성됩니다. 영문, 숫자, 언더스코어(_)만 사용 가능합니다.</p>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="persona_id" class="block text-sm font-medium text-gray-700 mb-2">페르소나 할당</label>
                    <select id="persona_id" name="persona_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">페르소나 없음</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                {{ $persona->name }} ({{ $persona->occupation }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">나중에 변경할 수 있습니다</p>
                </div>
            </div>
        </div>
        
        <!-- 비밀번호 설정 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-lock text-primary-500 mr-2"></i>
                비밀번호 설정
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">비밀번호 *</label>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror"
                           placeholder="최소 8자 이상" required>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">최소 8자 이상의 비밀번호를 입력하세요</p>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">비밀번호 확인 *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="비밀번호를 다시 입력하세요" required>
                    <p class="mt-1 text-xs text-gray-500">위에 입력한 비밀번호와 동일하게 입력하세요</p>
                </div>
            </div>
            
            <!-- 비밀번호 생성 도구 -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-medium text-gray-700">자동 비밀번호 생성</label>
                    <button type="button" id="generate-password" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        <i class="fas fa-magic mr-1"></i>
                        비밀번호 생성
                    </button>
                </div>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <label class="flex items-center">
                        <input type="checkbox" id="include-numbers" checked class="rounded mr-2">
                        숫자 포함
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" id="include-symbols" class="rounded mr-2">
                        특수문자 포함
                    </label>
                    <div class="flex items-center">
                        <span class="mr-2">길이:</span>
                        <input type="number" id="password-length" value="12" min="8" max="32" class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 계정 설정 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-cog text-primary-500 mr-2"></i>
                계정 설정
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div>
                        <h4 class="text-sm font-medium text-green-900">이메일 인증</h4>
                        <p class="text-sm text-green-700">관리자가 생성하는 계정은 자동으로 이메일 인증됩니다</p>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">초기 알림</h4>
                        <p class="text-sm text-blue-700">사용자에게 계정 생성 알림 이메일을 발송합니다</p>
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="send_welcome_email" value="0">
                            <input type="checkbox" name="send_welcome_email" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 미리보기 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-eye text-primary-500 mr-2"></i>
                계정 정보 미리보기
            </h3>
            
            <div id="user-preview" class="space-y-3">
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                        ?
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">이름을 입력하세요</p>
                        <p class="text-sm text-gray-500">이메일을 입력하세요</p>
                        <p class="text-xs text-gray-400">페르소나: 없음</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 버튼 -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.users') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                목록으로 돌아가기
            </a>
            
            <div class="flex items-center space-x-3">
                <button type="button" id="test-credentials"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-vial mr-2"></i>
                    입력값 검증
                </button>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    사용자 생성
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    const personaSelect = document.getElementById('persona_id');
    const generatePasswordBtn = document.getElementById('generate-password');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const testCredentialsBtn = document.getElementById('test-credentials');
    
    // 실시간 미리보기 업데이트
    function updatePreview() {
        const name = nameInput.value || '이름을 입력하세요';
        const email = emailInput.value || '이메일을 입력하세요';
        const personaOption = personaSelect.options[personaSelect.selectedIndex];
        const personaText = personaOption.value ? personaOption.text : '없음';
        
        const initial = name.charAt(0).toUpperCase() || '?';
        
        document.getElementById('user-preview').innerHTML = `
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-lg font-semibold">
                    ${initial}
                </div>
                <div>
                    <p class="font-medium text-gray-900">${name}</p>
                    <p class="text-sm text-gray-500">${email}</p>
                    <p class="text-xs text-gray-400">페르소나: ${personaText}</p>
                </div>
            </div>
        `;
    }
    
    // 이벤트 리스너 등록
    [nameInput, emailInput, personaSelect].forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // 사용자명 자동 생성
    emailInput.addEventListener('blur', function() {
        if (!usernameInput.value && emailInput.value) {
            const emailPart = emailInput.value.split('@')[0];
            const cleanUsername = emailPart.replace(/[^a-zA-Z0-9_]/g, '').toLowerCase();
            if (cleanUsername) {
                usernameInput.value = cleanUsername;
            }
        }
    });
    
    // 비밀번호 생성
    generatePasswordBtn.addEventListener('click', function() {
        const length = parseInt(document.getElementById('password-length').value) || 12;
        const includeNumbers = document.getElementById('include-numbers').checked;
        const includeSymbols = document.getElementById('include-symbols').checked;
        
        let chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (includeNumbers) chars += '0123456789';
        if (includeSymbols) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        let password = '';
        for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        passwordInput.value = password;
        passwordConfirmInput.value = password;
        
        // 비밀번호 강도 표시
        showPasswordStrength(password);
    });
    
    // 비밀번호 강도 체크
    function showPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        const strengthTexts = ['매우 약함', '약함', '보통', '강함', '매우 강함'];
        const strengthColors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600', 'text-blue-600'];
        
        // 임시 알림 표시
        const existingAlert = document.querySelector('.password-strength-alert');
        if (existingAlert) existingAlert.remove();
        
        const alert = document.createElement('div');
        alert.className = `password-strength-alert mt-2 p-2 bg-gray-100 rounded text-sm ${strengthColors[strength - 1] || 'text-gray-600'}`;
        alert.textContent = `비밀번호 강도: ${strengthTexts[strength - 1] || '없음'}`;
        generatePasswordBtn.parentNode.appendChild(alert);
        
        setTimeout(() => alert.remove(), 3000);
    }
    
    // 입력값 검증
    testCredentialsBtn.addEventListener('click', function() {
        const name = nameInput.value;
        const email = emailInput.value;
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        
        let errors = [];
        
        if (!name) errors.push('이름을 입력하세요');
        if (!email) errors.push('이메일을 입력하세요');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('올바른 이메일 형식이 아닙니다');
        if (!password) errors.push('비밀번호를 입력하세요');
        if (password.length < 8) errors.push('비밀번호는 최소 8자 이상이어야 합니다');
        if (password !== passwordConfirm) errors.push('비밀번호 확인이 일치하지 않습니다');
        
        // 결과 표시
        const existingResult = document.querySelector('.validation-result');
        if (existingResult) existingResult.remove();
        
        const result = document.createElement('div');
        result.className = 'validation-result mt-4 p-4 rounded-lg';
        
        if (errors.length === 0) {
            result.className += ' bg-green-50 border border-green-200 text-green-800';
            result.innerHTML = '<i class="fas fa-check-circle mr-2"></i>모든 입력값이 올바릅니다!';
        } else {
            result.className += ' bg-red-50 border border-red-200 text-red-800';
            result.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>다음 문제를 해결해주세요:<ul class="list-disc list-inside mt-2">' + 
                errors.map(error => `<li>${error}</li>`).join('') + '</ul>';
        }
        
        testCredentialsBtn.parentNode.appendChild(result);
        setTimeout(() => result.remove(), 5000);
    });
    
    // 초기 미리보기 생성
    updatePreview();
});
</script>
@endsection