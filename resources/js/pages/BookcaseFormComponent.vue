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
    <transition name="slide-fade">
        <div v-show="loaded">
            <header-component>
                <div class="flex justify-between w-full">
                    <history-back-button></history-back-button>
                    <div class="font-bold">책장관리</div>
                    <button type="button" class="px-3 py-1.5 bg-gray-700 text-white rounded-lg text-sm font-bold" @click="saveBookStore">저장</button>
                </div>
            </header-component>
            <div class="container-fluid max-w-xl mx-auto w-full">
                <div class="py-3 flex gap-9 items-center">
                    <div>
                        <div class="font-bold">비공개 책장</div>
                        <div class="text-sm text-gray-600">비공개 책장으로 전환하면 상대방이 회원님을 팔로우하지 않는 한 책장을 볼수 없게 됩니다.</div>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" v-model="bookcase.privacy" :true-value="1" :false-value="0">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div>
                    <input type="text" placeholder="책장명" class="w-full border rounded-xl border-gray-400" v-model="bookcase.title">
                    <div class="text-right">
                        <small class="text-gray-600">{{ bookcase.title ? bookcase.title.length : 0 }}/300</small>
                    </div>
                </div>
                <div class="mt-3">
                    <textarea name="" id="" cols="30" rows="10" class="w-full h-32 border rounded-xl border-gray-400" placeholder="책장 설명" v-model="bookcase.description"></textarea>
                </div>
                <div class="mt-3">
                    <div class="text-center">
                        <input type="text" placeholder="추가할 도서를 검색하세요" class="w-full rounded-t-xl border border-gray-400" @keyup="searchBooks" v-model="q">
                        <template v-if="books">
                            <div class="relative overflow-hidden border border-gray-400 border-t-0 rounded-b-xl">
                                <ul class="max-h-[300px] overflow-y-auto p-3 divide-y bg-white">
                                    <li class="py-2" v-for="book in books" :key="book.id">
                                        <div class="flex gap-3">
                                            <div class="w-20 h-20 bg-gray-300 rounded-lg border shrink-0 overflow-hidden">
                                                <img :src="book.cover_image" alt="">
                                            </div>
                                            <div class="flex justify-between w-full items-center">
                                                <div class="text-start w-full mr-3">
                                                    <div class="font-bold">{{ book.title }}</div>
                                                    <div class="text-sm mt-2">{{ book.author }}</div>
                                                    <div class="text-sm text-gray-600">{{ book.publisher }}</div>
                                                </div>
                                                <div>
                                                    <button type="button"
                                                            class="shrink-0 px-3 py-2 border border-gray-500 rounded-lg text-xs w-[60px]"
                                                            :disabled="book.checked"
                                                            @click="addBook(book.id)">
                                                        <span v-if="book.checked">추가됨</span>
                                                        <span v-else>추가</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="absolute left-0 bottom-0 right-0 h-[30px] bg-gradient-to-t from-white"></div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="py-2 text-sm border border-gray-400 border-t-0 rounded-b-xl bg-white">검색된 내용이 없습니다.</div>
                        </template>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="font-bold text-lg text-center">진열된 도서목록</div>
                    <ul class="mt-3" ref="bookcaseBooksEl">
                        <template v-if="bookcaseBooks && bookcaseBooks.length > 0">
                            <li class="bg-white p-3 rounded-xl my-3" v-for="book in bookcaseBooks" :key="book.id">
                                <div class="flex gap-3">
                                    <button type="button" class="handle">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"> <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> </svg>
                                    </button>
                                    <div class="w-20 h-20 bg-gray-300 rounded-lg border shrink-0 overflow-hidden">
                                        <img :src="book.cover_image" alt="">
                                    </div>
                                    <div class="flex justify-between w-full items-center">
                                        <div class="text-start w-full mr-3">
                                            <div class="font-bold flex justify-between">
                                                <span class="mr-3">{{ book.title }}</span>
                                                <dropdown-component>
                                                    <template #mybutton>
                                                        <button type="button" class="rounded-full text-center flex justify-center items-center shrink-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                 stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="1">
                                                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                            </svg>
                                                        </button>
                                                    </template>
                                                    <ul class="dropdown w-[100px] text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10">
                                                        <li class="px-3 py-2.5">
                                                            <button type="button" @click="deleteBook(book.id)">삭제</button>
                                                        </li>
                                                    </ul>
                                                </dropdown-component>
                                            </div>
                                            <div class="text-sm mt-2">{{ book.author }}</div>
                                            <div class="text-sm text-gray-600">{{ book.publisher }}</div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                        <template v-else>
                            <li class="py-2 text-sm text-center">등록된 도서가 없습니다.</li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </transition>
</template>
