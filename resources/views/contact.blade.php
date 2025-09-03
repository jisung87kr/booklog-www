<x-app-layout>
    @section('title', '문의 - ' . config('app.name'))
    @section('description', '북로그 커뮤니티에 문의사항이 있으신가요? 언제든지 문의해 주세요.')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex space-x-4 justify-center text-lg mb-6">
                <a href="{{ route('post.index') }}" class="px-4 py-2 text-gray-500">공지사항</a>
                <a href="{{ route('contact.create') }}" class="px-4 py-2 font-bold ">문의</a>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="max-w-2xl mx-auto">
                        <div class="text-center mb-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">문의하기</h1>
                            <p class="text-gray-600">궁금한 점이나 문의사항이 있으시면 언제든지 연락해 주세요. 빠른 시일 내에 답변드리겠습니다.</p>
                        </div>

                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg">
                                <div class="flex">
                                    <div class="ml-3">
                                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg">
                                <div class="flex">
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 mb-2">다음 오류를 확인해 주세요:</h3>
                                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름 *</label>
                                    <input type="text" id="name" name="name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('name') border-red-500 @enderror"
                                        placeholder="성함을 입력해 주세요" value="{{ old('name') }}">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일 *</label>
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror"
                                        placeholder="이메일 주소를 입력해 주세요" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                                <input type="text" id="subject" name="subject" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('subject') border-red-500 @enderror"
                                    placeholder="문의 제목을 입력해 주세요" value="{{ old('subject') }}">
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">문의 유형</label>
                                <select id="category" name="category"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('category') border-red-500 @enderror">
                                    <option value="">문의 유형을 선택해 주세요</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">메시지 *</label>
                                <textarea id="message" name="message" rows="6" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 resize-none @error('message') border-red-500 @enderror"
                                    placeholder="문의 내용을 자세히 적어주세요. 문제가 발생한 상황이나 요청사항을 구체적으로 설명해 주시면 더 정확한 답변을 드릴 수 있습니다.">{{ old('message') }}</textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="privacy" name="privacy" required
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="privacy" class="ml-3 text-sm text-gray-600">
                                    개인정보 수집 및 이용에 동의합니다. <a href="#" class="text-blue-600 hover:underline">자세히 보기</a>
                                </label>
                            </div>

                            <div class="flex justify-center">
                                <button type="submit"
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition duration-200 shadow-lg">
                                    문의 보내기
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
