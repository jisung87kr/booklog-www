<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { useUserStore } from '../stores/user.js';
import HeaderComponent from "../components/headerComponent.vue";
import DropdownComponent from "../components/DropdownComponent.vue";
import Sortable from 'sortablejs';
import {sendRequest} from "../common.js";
import debounce from 'lodash/debounce';

const userStore = useUserStore();
const auth = ref(userStore.user);
const bookcase = ref(window.__bookcase);
const profileUser = ref(window.__profileUser);
const loaded = ref(false);
const q = ref('');
const books = ref([]);
const bookcaseBooks = ref([]);
const bookcaseBooksEl = ref(null);

const fetchBooks = async () => {
    try {
        let params = {
            q: q.value,
        }
        return sendRequest('GET', '/api/service/books', params);
    } catch (error) {
        console.error(error);
    }
};

const searchBooks = debounce(async () => {
    const response = await fetchBooks();
    books.value = response.data.data;
    setChecked();
}, 300);

const addBook = (id) => {
    const book = books.value.find(book => book.id === id);
    book.checked = true;
    book.pivot = {
        book_id: book.id,
    };

    bookcaseBooks.value.push(book);
};
const deleteBook = (id) => {
    books.value = books.value.map(book => {
        if (book.id === id) {
            book.checked = false;
        }
        return book;
    });
    bookcaseBooks.value = bookcaseBooks.value.filter(book => book.id !== id);
};

const saveBookStore = async () => {
    try {
        if(bookcase.value.id) {
            let params = {
                title: bookcase.value.title,
                description: bookcase.value.description,
                privacy: bookcase.value.privacy,
                books: bookcaseBooks.value.map(book => book.pivot.book_id),
            }
            await sendRequest('PUT', `/api/users/${auth.value.username}/bookcases/${bookcase.value.id}`, params);
        } else {
            let params = {
                title: bookcase.value.title,
                description: bookcase.value.description,
                privacy: bookcase.value.privacy,
                books: bookcaseBooks.value.map(book => book.id),
            }
            const response = await sendRequest('POST', `/api/users/${auth.value.username}/bookcases`, params);
            if(response.data.id){
                window.location.href = `/@${auth.value.username}/bookcases/${response.data.id}`;
            }
        }
        alert('저장되었습니다.');
    } catch (error) {
        console.error(error);
    }
};

const setChecked = () => {
    let newBooks = books.value.map(book => {
        book.checked = bookcaseBooks.value.some(bookcaseBook => bookcaseBook.pivot.book_id === book.id);
        return book;
    });

    books.value = [...newBooks];
};

onMounted(async () => {
    loaded.value = true;

    //const response = await fetchBooks();
    //books.value = response.data.data;
    bookcaseBooks.value = bookcase.value.books ?? [];

    // setChecked();

    new Sortable(bookcaseBooksEl.value, {
        animation: 150,
        handle: '.handle',
        onEnd: (event) => {
            bookcaseBooks.value.splice(event.newIndex, 0, bookcaseBooks.value.splice(event.oldIndex, 1)[0]);
            bookcaseBooks.value.map((book, index) => {
                book.pivot.order = index;
            });
        }
    });
});

</script>
<template>
    <Transition name="slide-fade">
        <div v-show="loaded" class="min-h-screen bg-gray-50">
            <!-- Modern header -->
            <header-component class="sticky top-0 z-30 bg-white border-b border-gray-200 !max-w-full px-2 md:px-0 mb-6">
                <div class="flex justify-between items-center w-full max-w-2xl">
                    <history-back-button class="touch-manipulation"></history-back-button>
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <div class="font-bold text-gray-900">책장관리</div>
                    </div>
                    <button type="button" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl text-sm font-semibold shadow-sm transition-colors touch-manipulation" @click="saveBookStore">
                        저장
                    </button>
                </div>
            </header-component>

            <div class="container-fluid max-w-2xl mx-auto w-full px-4 sm:px-6 pb-20 sm:pb-8">
                <!-- Privacy settings card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-6 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3 flex-1">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 mb-1">비공개 책장</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">비공개 책장으로 전환하면 상대방이 회원님을 팔로우하지 않는 한 책장을 볼 수 없게 됩니다.</p>
                            </div>
                        </div>
                        <label class="inline-flex items-center cursor-pointer touch-manipulation ml-4">
                            <input type="checkbox" class="sr-only peer" v-model="bookcase.privacy" :true-value="1" :false-value="0">
                            <div class="relative w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-blue-600 shadow-sm"></div>
                        </label>
                    </div>
                </div>

                <!-- Bookcase details card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-6 mb-6">
                    <div class="space-y-6">
                        <!-- Title input -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>책장명</span>
                                </div>
                            </label>
                            <input
                                type="text"
                                placeholder="나만의 특별한 책장에 이름을 지어주세요"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm sm:text-base"
                                v-model="bookcase.title"
                                maxlength="300">
                            <div class="flex justify-between items-center mt-2">
                                <div class="text-xs text-gray-500">최대 300자까지 입력 가능합니다</div>
                                <div class="text-xs font-medium" :class="bookcase.title && bookcase.title.length > 250 ? 'text-red-500' : 'text-gray-500'">
                                    {{ bookcase.title ? bookcase.title.length : 0 }}/300
                                </div>
                            </div>
                        </div>

                        <!-- Description textarea -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    <span>책장 설명</span>
                                </div>
                            </label>
                            <textarea
                                placeholder="이 책장에 대한 설명을 작성해보세요. 어떤 주제의 책들인지, 왜 이 책들을 모으게 되었는지 등을 적어보세요."
                                class="w-full h-32 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none text-sm sm:text-base"
                                v-model="bookcase.description">
                            </textarea>
                        </div>
                    </div>
                </div>

                <!-- Book search card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">도서 검색</h3>
                                <p class="text-sm text-gray-600">책장에 추가할 도서를 검색해보세요</p>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                placeholder="도서명, 작가명, 출판사로 검색하세요"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm sm:text-base bg-white"
                                @keyup="searchBooks"
                                v-model="q">
                        </div>
                    </div>

                    <!-- Search results -->
                    <div v-if="q && q.length > 0" class="max-h-80 overflow-y-auto">
                        <template v-if="books && books.length > 0">
                            <div class="divide-y divide-gray-100">
                                <div class="p-4 hover:bg-gray-50 transition-colors" v-for="book in books" :key="book.id">
                                    <div class="flex gap-4">
                                        <div class="w-16 h-20 sm:w-20 sm:h-24 bg-gray-200 rounded-xl border-2 border-gray-100 shrink-0 overflow-hidden shadow-sm">
                                            <img :src="book.cover_image || 'https://placehold.co/400x600'" :alt="book.title" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex justify-between w-full items-center min-w-0">
                                            <div class="flex-1 min-w-0 mr-4">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ book.title }}</h4>
                                                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ book.author }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ book.publisher }}</p>
                                            </div>
                                            <button
                                                type="button"
                                                class="shrink-0 px-4 py-2 border rounded-full text-xs sm:text-sm font-medium transition-colors touch-manipulation"
                                                :class="book.checked ?
                                                    'border-green-200 bg-green-50 text-green-700 cursor-not-allowed' :
                                                    'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100 active:bg-blue-200'"
                                                :disabled="book.checked"
                                                @click="addBook(book.id)">
                                                <span v-if="book.checked" class="flex items-center space-x-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>추가됨</span>
                                                </span>
                                                <span v-else>추가하기</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">검색된 도서가 없습니다</p>
                                <p class="text-xs text-gray-400 mt-1">다른 검색어를 시도해보세요</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Selected books card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-red-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">진열된 도서목록</h3>
                                    <p class="text-sm text-gray-600">드래그해서 순서를 변경하세요</p>
                                </div>
                            </div>
                            <div class="text-xs sm:text-sm text-gray-500 bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm">
                                {{ bookcaseBooks?.length || 0 }}권
                            </div>
                        </div>
                    </div>

                    <div class="min-h-[200px]">
                        <ul ref="bookcaseBooksEl">
                            <template v-if="bookcaseBooks && bookcaseBooks.length > 0">
                                <li class="p-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors" v-for="book in bookcaseBooks" :key="book.id">
                                    <div class="flex gap-4 items-start">
                                        <button type="button" class="handle p-2 hover:bg-gray-100 rounded-lg transition-colors touch-manipulation">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="2" class="text-gray-400">
                                                <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            </svg>
                                        </button>
                                        <div class="w-16 h-20 sm:w-20 sm:h-24 bg-gray-200 rounded-xl border-2 border-gray-100 shrink-0 overflow-hidden shadow-sm">
                                            <img :src="book.cover_image || 'https://placehold.co/400x600'" :alt="book.title" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex justify-between w-full items-start min-w-0">
                                            <div class="flex-1 min-w-0 mr-4">
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ book.title }}</h4>
                                                <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ book.author }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ book.publisher }}</p>
                                            </div>
                                            <dropdown-component>
                                                <template #mybutton>
                                                    <button type="button" class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors touch-manipulation">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="2" class="text-gray-500">
                                                            <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                            <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                        </svg>
                                                    </button>
                                                </template>
                                                <ul class="dropdown w-32 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-xl shadow-lg absolute right-0 top-10 z-10 py-2">
                                                    <li>
                                                        <button type="button" @click="deleteBook(book.id)" class="w-full px-4 py-2.5 text-left hover:bg-red-50 text-red-600 transition-colors touch-manipulation">
                                                            삭제
                                                        </button>
                                                    </li>
                                                </ul>
                                            </dropdown-component>
                                        </div>
                                    </div>
                                </li>
                            </template>
                            <template v-else>
                                <div class="p-8 sm:p-12 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">등록된 도서가 없습니다</h3>
                                    <p class="text-gray-500">위에서 도서를 검색하여 책장에 추가해보세요</p>
                                </div>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
