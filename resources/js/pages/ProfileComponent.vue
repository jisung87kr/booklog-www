<script setup>
import {ref, onMounted, onBeforeUnmount, nextTick, watch, onUpdated} from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user.js';
import ProfileModalComponent from "../components/ProfileModalComponent.vue";
import BookcaseComponent from "../components/BookcaseComponent.vue";
import Sortable from "sortablejs";
import cloneDeep from "lodash/cloneDeep";
import {sendRequest} from "../common.js";
import ModalComponent from "../components/ModalComponent.vue";

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
const followModalShow = ref(false);
const followModalType = ref(null);
const userList = ref([]);

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
            handle: '.handle',
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

const logout = async () => {
    axios.post('/logout').then(res => {
        location.href = '/';
    }).catch(err => {
        console.error(err);
    });
};

const openFollowModal = async (type) => {
    let response;
    switch (type) {
        case 'follower':
            followModalType.value = '팔로우';
            response = await sendRequest('GET', `/api/users/${user.value.username}/followers`);
            userList.value = response.data;
            break;
        case 'following':
            followModalType.value = '팔로우 중';
            response = await sendRequest('GET', `/api/users/${user.value.username}/followings`);
            userList.value = response.data;
            break;
    }

    followModalShow.value = true;
};

const closeFollowModal = () => {
    followModalShow.value = false;
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
                    <div class="bg-white shadow w-full rounded-2xl max-w-xl overflow-hidden">
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
                                <div>
                                    <div class="text-sm text-zinc-500 flex">
                                        <button type="button" @click="openFollowModal('follower')">팔로워 <span v-html="user.followers_count"></span>명</button>
                                        <span class="px-1">∙</span>
                                        <button type="button" @click="openFollowModal('following')">팔로우 중 <span v-html="user.following_count"></span>명</button>
                                        <teleport to="body">
                                            <modal-component :is-visible="followModalShow" @close="closeFollowModal">
                                                <div v-if="userList.data.length> 0"
                                                     class="divide-y p-6"
                                                >
                                                    <div class="font-bold mb-3 text-lg">{{followModalType}}</div>
                                                    <template v-for="user in userList.data" :key="user.id">
                                                        <avatar-component :user="user" class="py-4"></avatar-component>
                                                    </template>
                                                </div>
                                                <div v-else>
                                                    <div class="p-6">
                                                        <span v-html="followModalType"></span>
                                                        <span>중인 사람이 없습니다.</span>
                                                    </div>
                                                </div>
                                            </modal-component>
                                        </teleport>
                                    </div>
                                    <template v-if="user.link">
                                        <div class="text-sm text-zinc-500 flex">
                                            <a :href="user.link" target="_blank" v-html="user.link"></a>
                                        </div>
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
