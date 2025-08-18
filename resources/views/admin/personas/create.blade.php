@extends('admin.layout')

@section('title', '페르소나 생성')
@section('header', '새 페르소나 생성')
@section('description', '새로운 AI 페르소나를 생성합니다')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.personas.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- 기본 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user text-primary-500 mr-2"></i>
                기본 정보
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">페르소나 이름 *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           placeholder="예: 독서광 김서연" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">직업 *</label>
                    <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('occupation') border-red-500 @enderror"
                           placeholder="예: 북카페 사장, 문학 교수, 프리랜서 작가" required>
                    @error('occupation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">성별 *</label>
                    <select id="gender" name="gender" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('gender') border-red-500 @enderror" required>
                        <option value="">성별 선택</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>남성</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>여성</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>기타</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">나이 *</label>
                    <input type="number" id="age" name="age" value="{{ old('age') }}" min="1" max="120"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('age') border-red-500 @enderror"
                           placeholder="예: 28" required>
                    @error('age')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label for="speaking_style" class="block text-sm font-medium text-gray-700 mb-2">말투/성격</label>
                <input type="text" id="speaking_style" name="speaking_style" value="{{ old('speaking_style') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="예: 친근하고 따뜻한, 지적이고 신중한, 활발하고 유머러스한">
            </div>
            
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">페르소나 설명</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                          placeholder="이 페르소나의 특징, 성격, 배경 등을 자세히 설명해주세요.">{{ old('description') }}</textarea>
            </div>
        </div>
        
        <!-- 독서 취향 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-book text-primary-500 mr-2"></i>
                독서 취향
            </h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">선호 장르</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @php
                            $genres = ['소설', '시/에세이', '인문학', '철학', '심리학', '경제/경영', 
                                     '자기계발', '과학', '기술/IT', '역사', '예술', '종교', 
                                     '여행', '요리', '건강', '육아', '만화', '판타지', '미스터리', 'SF'];
                            $oldGenres = old('genres', []);
                        @endphp
                        @foreach($genres as $genre)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="genres[]" value="{{ $genre }}" 
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mr-2"
                                       {{ in_array($genre, $oldGenres) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $genre }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <label for="authors" class="block text-sm font-medium text-gray-700 mb-2">선호 작가</label>
                    <input type="text" id="authors" name="authors" value="{{ old('authors') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="쉼표(,)로 구분하여 입력 예: 무라카미 하루키, 한강, 정유정">
                    <p class="mt-1 text-xs text-gray-500">쉼표로 구분하여 여러 작가를 입력할 수 있습니다</p>
                </div>
                
                <div>
                    <label for="reading_style" class="block text-sm font-medium text-gray-700 mb-2">독서 스타일</label>
                    <select id="reading_style" name="reading_style" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">선택 안함</option>
                        <option value="집중형" {{ old('reading_style') == '집중형' ? 'selected' : '' }}>집중형 (깊이 있게 읽기)</option>
                        <option value="다독형" {{ old('reading_style') == '다독형' ? 'selected' : '' }}>다독형 (많이 읽기)</option>
                        <option value="속독형" {{ old('reading_style') == '속독형' ? 'selected' : '' }}>속독형 (빠르게 읽기)</option>
                        <option value="감상형" {{ old('reading_style') == '감상형' ? 'selected' : '' }}>감상형 (여운을 즐기며 읽기)</option>
                        <option value="분석형" {{ old('reading_style') == '분석형' ? 'selected' : '' }}>분석형 (비판적 사고로 읽기)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- 버튼 -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.personas') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                목록으로 돌아가기
            </a>
            
            <div class="flex items-center space-x-3">
                <button type="button" id="preview-btn"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    미리보기
                </button>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    페르소나 생성
                </button>
            </div>
        </div>
    </form>
</div>

<!-- 미리보기 모달 -->
<div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">페르소나 미리보기</h3>
                <button id="close-preview" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="preview-content" class="space-y-4">
                <!-- 미리보기 내용이 여기에 동적으로 생성됩니다 -->
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <button id="close-preview-btn" class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    닫기
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('preview-btn');
    const previewModal = document.getElementById('preview-modal');
    const closePreview = document.getElementById('close-preview');
    const closePreviewBtn = document.getElementById('close-preview-btn');
    const previewContent = document.getElementById('preview-content');
    
    previewBtn.addEventListener('click', function() {
        generatePreview();
        previewModal.classList.remove('hidden');
    });
    
    closePreview.addEventListener('click', () => previewModal.classList.add('hidden'));
    closePreviewBtn.addEventListener('click', () => previewModal.classList.add('hidden'));
    
    function generatePreview() {
        const name = document.getElementById('name').value || '이름 없음';
        const occupation = document.getElementById('occupation').value || '직업 미설정';
        const gender = document.getElementById('gender').value || '성별 미선택';
        const age = document.getElementById('age').value || '나이 미설정';
        const speakingStyle = document.getElementById('speaking_style').value || '기본 말투';
        const description = document.getElementById('description').value || '설명 없음';
        const authors = document.getElementById('authors').value || '선호 작가 없음';
        const readingStyle = document.getElementById('reading_style').value || '독서 스타일 미선택';
        
        const selectedGenres = Array.from(document.querySelectorAll('input[name="genres[]"]:checked'))
            .map(checkbox => checkbox.value);
        
        const genderText = {'male': '남성', 'female': '여성', 'other': '기타'}[gender] || gender;
        
        previewContent.innerHTML = `
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-semibold">
                    ${name.charAt(0)}
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-900">${name}</h4>
                    <p class="text-gray-600">${occupation}</p>
                    <p class="text-sm text-gray-500">${genderText} • ${age}세</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">말투/성격</h5>
                    <p class="text-gray-700">${speakingStyle}</p>
                </div>
                
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">설명</h5>
                    <p class="text-gray-700">${description}</p>
                </div>
                
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">선호 장르</h5>
                    <div class="flex flex-wrap gap-2">
                        ${selectedGenres.length > 0 ? 
                            selectedGenres.map(genre => `<span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">${genre}</span>`).join('') :
                            '<span class="text-gray-500">선택된 장르 없음</span>'
                        }
                    </div>
                </div>
                
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">선호 작가</h5>
                    <p class="text-gray-700">${authors}</p>
                </div>
                
                <div>
                    <h5 class="font-medium text-gray-900 mb-2">독서 스타일</h5>
                    <p class="text-gray-700">${readingStyle}</p>
                </div>
            </div>
        `;
    }
});

// 폼 제출 전 JSON 데이터 준비
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedGenres = Array.from(document.querySelectorAll('input[name="genres[]"]:checked'))
        .map(checkbox => checkbox.value);
    
    const authors = document.getElementById('authors').value
        .split(',')
        .map(author => author.trim())
        .filter(author => author);
    
    const readingStyle = document.getElementById('reading_style').value;
    
    const preferences = {
        genres: selectedGenres,
        authors: authors,
        reading_style: readingStyle
    };
    
    // hidden input 생성
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'reading_preferences';
    hiddenInput.value = JSON.stringify(preferences);
    
    this.appendChild(hiddenInput);
});
</script>
@endsection