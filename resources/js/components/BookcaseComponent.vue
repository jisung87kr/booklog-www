<script setup>
import { ref, defineProps } from 'vue';
import DropdownComponent from "./DropdownComponent.vue";
import {useUserStore} from "../stores/user.js";
import BookComponent from "./BookComponent.vue";

const userStore = useUserStore();
const auth = ref(userStore.user);
const type = ref('bookcase');

const props = defineProps({
    bookcase: Object,
});

</script>
<template>
    <div class="bg-white">
        <div class="text-center" v-if="auth && auth.id == bookcase.user.id">
            <button type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16"
                     height="16" stroke-width="2">
                    <path d="M5 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M5 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M12 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M12 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M19 9m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                    <path d="M19 15m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                </svg>
            </button>
        </div>
        <div class="flex">
            <div class="w-full">
                <div>{{ props.bookcase.title }}</div>
                <div class="text-sm text-gray-600">{{ props.bookcase.description }}</div>
                <div class="mt-2 flex gap-3">
                    <like-button :model="bookcase" :auth="auth" :type="type"></like-button>
                    <comment-button :model="bookcase" :type="type"></comment-button>
                    <share-button :model="bookcase" :type="type"></share-button>
                </div>
            </div>
            <div class="shrink-0 w-30 flex gap-1">
                <div>
                    <a :href="'/@'+bookcase.user.username+'/bookcases/'+bookcase.id" class="border rounded-lg px-3 py-2 text-xs font-bold text-gray-900 block">책장보기</a>
                </div>
                <dropdown-component v-if="auth">
                    <template v-slot:mybutton>
                        <button type="button" class="border rounded-lg px-2 py-2 text-sm font-bold text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots" width="16"
                                 height="16"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                            </svg>
                        </button>
                    </template>

                    <ul class="dropdown w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10">
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200 rounded-t-lg">
                            <user-action-button :actionable-type="type" action-name="bookmark" :model="bookcase" :auth="auth">
                                <span>저장</span><span v-if="bookcase && bookcase.bookmark_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200">
                            <user-action-button :actionable-type="type" action-name="uninterested" :model="bookcase" :auth="auth">
                                <span>관심없음</span><span v-if="bookcase && bookcase.uninterested_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200">
                            <user-action-button :actionable-type="type" action-name="block" :model="bookcase" :auth="auth">
                                <span>차단하기</span><span v-if="bookcase && bookcase.block_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200" v-if="auth.id !== bookcase.user_id">
                            <user-action-button :actionable-type="type" action-name="report" :model="bookcase" :auth="auth">
                                <span>신고하기</span><span v-if="bookcase && bookcase.report_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 rounded-b-lg">
                            <user-action-button :actionable-type="type" action-name="share" :model="bookcase" :auth="auth">
                                <span>링크복사</span>
                            </user-action-button>
                        </li>
                    </ul>
                </dropdown-component>
            </div>
        </div>
        <div class="text-xs text-gray-600 mt-3">{{ props.bookcase.books.length}}개 의 도서</div>
        <div class="overflow-x-auto mt-1">
            <div class="flex gap-3" v-if="props.bookcase.books.length > 0">
                <template v-for="book in props.bookcase.books" :key="book.id">
                    <book-component :book="book" class="shrink-0 w-[170px]"></book-component>
                </template>
            </div>
        </div>
    </div>
</template>
