@extends('admin.layout')

@section('title', '페르소나 편집')
@section('header', $persona->name . ' 편집')
@section('description', '페르소나 정보를 수정합니다')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.personas.update', $persona) }}" method="POST" class="space-y-6">
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
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">페르소나 이름 *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $persona->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           placeholder="예: 독서광 김서연" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">직업 *</label>
                    <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $persona->occupation) }}"
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
                        <option value="male" {{ old('gender', $persona->gender) == 'male' ? 'selected' : '' }}>남성</option>
                        <option value="female" {{ old('gender', $persona->gender) == 'female' ? 'selected' : '' }}>여성</option>
                        <option value="other" {{ old('gender', $persona->gender) == 'other' ? 'selected' : '' }}>기타</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">나이 *</label>
                    <input type="number" id="age" name="age" value="{{ old('age', $persona->age) }}" min="1" max="120"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('age') border-red-500 @enderror"
                           placeholder="예: 28" required>
                    @error('age')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="speaking_style" class="block text-sm font-medium text-gray-700 mb-2">말투/성격</label>
                <input type="text" id="speaking_style" name="speaking_style" value="{{ old('speaking_style', $persona->speaking_style) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                       placeholder="예: 친근하고 따뜻한, 지적이고 신중한, 활발하고 유머러스한">
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">페르소나 설명</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                          placeholder="이 페르소나의 특징, 성격, 배경 등을 자세히 설명해주세요.">{{ old('description', $persona->description) }}</textarea>
            </div>
        </div>

        <!-- 독서 취향 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-book text-primary-500 mr-2"></i>
                독서 취향
            </h3>

            @php
                $preferences = $persona->reading_preferences ?? [];
                $selectedGenres = $preferences['genres'] ?? [];
                $selectedAuthors = isset($preferences['authors']) ? implode(', ', $preferences['authors']) : '';
                $selectedStyle = $preferences['reading_style'] ?? '';
                $keywords = isset($preferences['keywords']) ? implode(', ', $preferences['keywords']) : '';
            @endphp

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">선호 장르</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @php
                            $genres = ['소설', '시/에세이', '인문학', '철학', '심리학', '경제/경영',
                                     '자기계발', '과학', '기술/IT', '역사', '예술', '종교',
                                     '여행', '요리', '건강', '육아', '만화', '판타지', '미스터리', 'SF'];
                        @endphp
                        @foreach($genres as $genre)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="genres[]" value="{{ $genre }}"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 mr-2"
                                       {{ in_array($genre, $selectedGenres) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $genre }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="authors" class="block text-sm font-medium text-gray-700 mb-2">선호 작가</label>
                    <input type="text" id="authors" name="authors" value="{{ old('authors', $selectedAuthors) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="쉼표(,)로 구분하여 입력 예: 무라카미 하루키, 한강, 정유정">
                    <p class="mt-1 text-xs text-gray-500">쉼표로 구분하여 여러 작가를 입력할 수 있습니다</p>
                </div>

                <div>
                    <label for="reading_style" class="block text-sm font-medium text-gray-700 mb-2">독서 스타일</label>
                    <select id="reading_style" name="reading_style"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">선택 안함</option>
                        <option value="집중형" {{ old('reading_style', $selectedStyle) == '집중형' ? 'selected' : '' }}>집중형 (깊이 있게 읽기)</option>
                        <option value="다독형" {{ old('reading_style', $selectedStyle) == '다독형' ? 'selected' : '' }}>다독형 (많이 읽기)</option>
                        <option value="속독형" {{ old('reading_style', $selectedStyle) == '속독형' ? 'selected' : '' }}>속독형 (빠르게 읽기)</option>
                        <option value="감상형" {{ old('reading_style', $selectedStyle) == '감상형' ? 'selected' : '' }}>감상형 (여운을 즐기며 읽기)</option>
                        <option value="분석형" {{ old('reading_style', $selectedStyle) == '분석형' ? 'selected' : '' }}>분석형 (비판적 사고로 읽기)</option>
                    </select>
                </div>

                <div>
                    <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">키워드</label>
                    <input type="text" id="keywords" name="keywords" value="{{ old('keywords', $keywords) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="쉼표(,)로 구분하여 입력 예: 성장, 힐링, 추리, 로맨스">
                    <p class="mt-1 text-xs text-gray-500">쉼표로 구분하여 여러 키워드를 입력할 수 있습니다</p>
                </div>
            </div>
        </div>

        <!-- 활성 상태 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-toggle-on text-primary-500 mr-2"></i>
                페르소나 설정
            </h3>

            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900">페르소나 활성화</h4>
                    <p class="text-sm text-gray-500">비활성화하면 새로운 사용자에게 할당되지 않습니다</p>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-500 mr-3">비활성</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $persona->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                    <span class="text-sm text-gray-500 ml-3">활성</span>
                </div>
            </div>
        </div>

        <!-- 통계 정보 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-primary-500 mr-2"></i>
                사용 통계
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $persona->users()->count() }}</div>
                    <div class="text-sm text-blue-600">할당된 사용자</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    @php
                        $aiPosts = \App\Models\Post::whereJsonContains('meta->persona_id', $persona->id)->count();
                    @endphp
                    <div class="text-2xl font-bold text-green-600">{{ $aiPosts }}</div>
                    <div class="text-sm text-green-600">생성된 AI 포스트</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ $persona->created_at->diffInDays(now()) }}</div>
                    <div class="text-sm text-purple-600">생성 후 일수</div>
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
                <!-- 활성화/비활성화 토글 -->
                <form action="{{ route('admin.personas.toggle', $persona) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium {{ $persona->is_active ? 'text-yellow-700 bg-yellow-50 border-yellow-200' : 'text-green-700 bg-green-50 border-green-200' }} hover:bg-opacity-80 transition-colors">
                        <i class="fas fa-{{ $persona->is_active ? 'pause' : 'play' }} mr-2"></i>
                        {{ $persona->is_active ? '비활성화' : '활성화' }}
                    </button>
                </form>

                <!-- 삭제 -->
                @if($persona->users()->count() == 0)
                    <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" class="inline"
                          onsubmit="return confirm('정말로 이 페르소나를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            삭제
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

<script>
// 폼 제출 전 JSON 데이터 준비
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedGenres = Array.from(document.querySelectorAll('input[name="genres[]"]:checked'))
        .map(checkbox => checkbox.value);

    const authors = document.getElementById('authors').value
        .split(',')
        .map(author => author.trim())
        .filter(author => author);

    const readingStyle = document.getElementById('reading_style').value;

    const keywords = document.getElementById('keywords').value
        .split(',')
        .map(keyword => keyword.trim())
        .filter(keyword => keyword);

    const preferences = {
        genres: selectedGenres,
        authors: authors,
        reading_style: readingStyle,
        keywords: keywords
    };

    // hidden input 생성 또는 업데이트
    let hiddenInput = document.querySelector('input[name="reading_preferences"]');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'reading_preferences';
        this.appendChild(hiddenInput);
    }
    hiddenInput.value = JSON.stringify(preferences);
});
</script>
@endsection
