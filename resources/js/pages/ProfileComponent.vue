<script setup>
import {ref, onMounted, onBeforeUnmount, nextTick, watch, onUpdated} from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user.js';
import ProfileModalComponent from "../components/ProfileModalComponent.vue";
import BookcaseComponent from "../components/BookcaseComponent.vue";
import Sortable from "sortablejs";
import cloneDeep from "lodash/cloneDeep";
import {sendRequest} from "../common.js";

// 사용자 인증 스토어 사용 설정
const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const loaded = ref(false);

const selectedActivityType = ref('bookcase');
const activityTypes = [
    { key: 'bookcase', value: '책장' },
    { key: 'post', value: '포스트' },
    { key: 'reply', value: '답글' },
    { key: 'quotation', value: '리포스트' },
];

const user = ref(window.__profileUser);
const showProfileModal = ref(true);
const sortableEl = ref(null);
const sortableInstance = ref(null);

const list = ref({
    current_page: 1,
    data: [],
    last_page: null,
    total: null,
});
const loading = ref(false);
const contentModalOpen = ref(false);
const selectedFeed = ref({
    id: null,
    user: {
        name: null,
        profile_photo_url: null,
    },
    note: null,
    images: [],
});


const fetchBookcases = async () => {
    try {
        let params = {
            user_id: user.value.id,
        };
        const response = await axios.get(`/api/users/${user.value.username}/bookcases`, {
            params: params,
        });
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchPosts = async () => {
    try {
        let params = {
            user_id: user.value.id,
        };
        const response = await axios.get(`/api/posts`, {
            params: params,
        });
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchReplies = async () => {
    try {
        const response = await axios.get(`/api/users/${user.value.username}/activity/replies`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchQuotation = async () => {
    try {
        const response = await axios.get(`/api/users/${user.value.username}/activity/quotations`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const handleScroll = async () => {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight * 0.8 && !loading.value && list.value.current_page < list.value.last_page) {
        const nextPage = list.value.current_page + 1;
        await getList(nextPage);
    }
};

const clickTab = async (activityType) => {
    selectedActivityType.value = activityType.key;
    await getList(1);
    await nextTick(i => {
        initSortable();
    });
};

const getList = async (page) => {
    if (page === 1) {
        list.value.data = [];
    }

    let response = {};
    switch (selectedActivityType.value) {
        case 'bookcase':
            response = await fetchBookcases();
            break;
        case 'post':
            response = await fetchPosts();
            break;
        case 'reply':
            response = await fetchReplies();
            break;
        case 'quotation':
            response = await fetchQuotation();
            break;
    }

    if (response && response.data) {
        list.value.data = [...list.value.data, ...response.data.data];
        list.value.current_page = response.data.current_page;
        list.value.last_page = response.data.last_page;
        list.value.total = response.data.total;
    }
};

const updateBookcaseOrder = async () => {
    let params = {
        bookcases: list.value.data,
    };
    const response = await sendRequest('PUT', `/api/users/${auth.value.username}/bookcases/order`, params);
    console.log(response);
}

const initSortable = () => {
    if (selectedActivityType.value === 'bookcase') {
        sortableInstance.value = new Sortable(sortableEl.value, {
            animation: 150,
            onEnd: (event) => {
                let bookcase = [...list.value.data];
                bookcase.splice(event.newIndex, 0, bookcase.splice(event.oldIndex, 1)[0]);

                bookcase = bookcase.map((item, index) => {
                    return {
                        ...item,
                        order: index,
                    };
                });

                list.value.data = bookcase;
                updateBookcaseOrder();
            },
        });
    }
};

onMounted(async () => {
    window.addEventListener("scroll", handleScroll);
    await getList(1);
    loaded.value = true;
    await nextTick();
    initSortable();
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>
<template>
    <Transition name="slide-fade">
        <div v-show="loaded">
            <header-component>
                <div class="flex justify-between w-full">
                    <history-back-button></history-back-button>
                    <div class="font-bold">프로필</div>
                    <div></div>
                </div>
            </header-component>
            <div class="container-fluid mx-auto w-full">
                <div class="flex justify-center mt-3 md:mt-0">
                    <div class="bg-white shadow w-full rounded-2xl max-w-xl">
                        <div class="p-6">
                            <div class="flex justify-between">
                                <div class="mr-3">
                                    <div class="text-3xl font-bold" v-html="user.name"></div>
                                    <div v-html="user.username"></div>
                                </div>
                                <div>
                                    <img :src="user.profile_photo_url" alt="" class="w-20 h-20 rounded-full bg-red border">
                                </div>
                            </div>
                            <div class="mt-3"
                                 v-if="user.description"
                                 v-html="user.description"
                            >
                            </div>
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-sm text-zinc-500 flex">
                                    <div>팔로워 <span v-html="user.followers_count"></span>명</div>
                                    <template v-if="user.user_link">
                                        <span class="px-1">∙</span><a :href="user.user_link" target="_blank" v-html="user.user_link"></a>
                                    </template>
                                </div>
                                <template v-if="auth && user.id != auth.id">
                                    <dropdown-component>
                                        <template v-slot:mybutton>
                                            <button type="button" class="rounded-full border border-zinc-900 w-6 h-6 text-center flex justify-center items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="1">
                                                    <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                    <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                </svg>
                                            </button>
                                        </template>
                                        <ul class="dropdown w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10">
                                            <li>
                                                <user-action-button actionable-type="user"
                                                                    action-name="share"
                                                                    :model="user"
                                                                    :auth="auth"
                                                                    class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                                >
                                                    <span>링크복사</span>
                                                </user-action-button>
                                            </li>
                                            <li>
                                                <user-action-button actionable-type="user"
                                                                    action-name="show_profile"
                                                                    :model="user"
                                                                    :auth="auth"
                                                                    class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                                >
                                                    <span>이 프로필 정보</span>
                                                </user-action-button>
                                            </li>
                                            <li>
                                                <user-action-button actionable-type="user"
                                                                    action-name="block"
                                                                    :model="user"
                                                                    :auth="auth"
                                                                    class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                                >
                                                    <span>차단하기</span><span v-if="user && user.block_id"> 취소</span>
                                                </user-action-button>
                                            </li>
                                            <li>
                                                <user-action-button actionable-type="user"
                                                                    action-name="report"
                                                                    :model="user"
                                                                    :auth="auth"
                                                                    class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                                >
                                                    <span>신고하기</span><span v-if="user && user.report_id"> 취소</span>
                                                </user-action-button>
                                            </li>
                                        </ul>
                                    </dropdown-component>

                                </template>
                            </div>
                            <template v-if="auth && user.id == auth.id">
                                <profile-modal-component :user="user"></profile-modal-component>
                            </template>
                            <template v-else>
                                <div class="grid grid-cols-2 gap-4 my-6">
                                    <follow-toggle-button :user="user"></follow-toggle-button>
                                    <button type="button"
                                            class="rounded-lg px-3 py-2 border border-zinc-300 text-sm font-bold"
                                    >언급</button>
                                </div>
                            </template>
                        </div>
                        <div class="grid grid-cols-4">
                            <template v-for="type in activityTypes"
                                      :key="type.key">
                                <button type="button"
                                        class="text-sm px-3 py-2 font-bold border-b"
                                        :class="type.key == selectedActivityType ? 'border-zinc-900 text-zinc-900' : 'border-zinc-300 text-zinc-500'"
                                        @click="clickTab(type)"
                                >{{type.value}}</button>
                            </template>
                        </div>
                        <template v-if="list.data.length > 0">
                            <div class="mt-4 text-center" v-if="auth && user.id == auth.id && selectedActivityType == 'bookcase'">
                                <a :href="'/@'+auth.username+'/bookcases/create'" class="rounded-xl px-3 py-2 border border-zinc-300 text-sm font-bold text-center inline-block">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2" class="mr-1"> <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path> <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path> <path d="M16 5l3 3"></path> </svg>
                                        <span>책장추가</span>
                                    </div>
                                </a>
                            </div>
                            <div class="divide-y" ref="sortableEl">
                                <template v-if="selectedActivityType == 'bookcase'">
                                    <bookcase-component v-for="bookcase in list.data"
                                                        :key="bookcase.id"
                                                        :bookcase="bookcase"
                                                        :auth="auth"
                                                        class="p-6"
                                    ></bookcase-component>
                                </template>
                                <template v-if="selectedActivityType == 'post'">
                                    <feed-component v-for="post in list.data"
                                                    :key="post.id"
                                                    :feed="post"
                                                    :auth="auth"
                                                    class="p-6"
                                    ></feed-component>
                                </template>
                                <template v-else-if="selectedActivityType == 'reply'">
                                    <comment-component v-for="comment in list.data"
                                                       :key="comment.id"
                                                       :comment="comment"
                                                       :auth="auth"
                                                       class="p-6"
                                    ></comment-component>
                                </template>
                                <template v-else-if="selectedActivityType == 'quotation'">
                                    <feed-component v-for="quotation in list.data"
                                                    :key="quotation.id"
                                                    :feed="quotation"
                                                    :auth="auth"
                                                    class="p-6"
                                    ></feed-component>
                                </template>
                            </div>
                        </template>
                        <template v-else>
                            <div class="p-6 text-gray-500 text-sm font-bold">데이터가 존재하지 않습니다.</div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
