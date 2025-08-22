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
import HeaderComponent from "../components/headerComponent.vue";

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
        <div v-show="loaded" class="min-h-screen bg-gray-50">
            <!-- Clean header -->
            <header-component class="sticky top-0 z-30 bg-white border-b border-gray-200 !max-w-full px-2 md:px-0 mb-6">
                <div class="flex justify-between items-center max-w-2xl w-full">
                    <history-back-button class="touch-manipulation"></history-back-button>
                    <div class="font-bold text-gray-900">프로필</div>
                    <div class="w-6"></div>
                </div>
            </header-component>

            <div class="container-fluid mx-auto w-full px-0 sm:px-4 pb-20 sm:pb-8">
                <div class="flex justify-center">
                    <!-- Modern profile container -->
                    <div class="w-full max-w-2xl">
                        <!-- Profile card -->
                        <div class="bg-white border-b sm:border sm:rounded-2xl sm:shadow-sm overflow-hidden mb-0 sm:mb-6">
                            <!-- Cover background with gradient -->
                            <div class="h-32 sm:h-40 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 relative">
                                <div class="absolute inset-0 bg-black/10"></div>
                            </div>

                            <div class="px-4 sm:px-6 pb-6">
                                <!-- Profile image and basic info -->
                                <div class="relative -mt-16 sm:-mt-20 mb-4">
                                    <div class="relative inline-block">
                                        <img :src="user.profile_photo_url" :alt="user.name"
                                             class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl border-4 border-white shadow-lg bg-white object-cover">
                                        <!-- Online status -->
                                        <div class="absolute -bottom-1 -right-1 w-6 h-6 sm:w-8 sm:h-8 bg-green-500 rounded-full border-3 border-white flex items-center justify-center">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Name and username -->
                                <div class="mb-4">
                                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1" v-html="user.name"></h1>
                                    <p class="text-gray-600" v-html="'@' + user.username"></p>
                                </div>

                                <!-- Bio -->
                                <div class="mb-4" v-if="user.description">
                                    <p class="text-gray-800 leading-relaxed" v-html="user.description"></p>
                                </div>

                                <!-- Meta info -->
                                <div class="flex items-center flex-wrap gap-4 text-sm text-gray-600 mb-4">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>대한민국</span>
                                    </div>
                                    <template v-if="user.link">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                            <a :href="user.link" target="_blank" class="text-blue-600 hover:text-blue-700 hover:underline" v-html="user.link"></a>
                                        </div>
                                    </template>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>2023년 6월 가입</span>
                                    </div>
                                </div>

                                <!-- Stats -->
                                <div class="flex items-center justify-around sm:justify-start sm:space-x-6 mb-6">
                                    <button type="button" @click="openFollowModal('follower')"
                                            class="group text-center hover:bg-gray-50 rounded-lg px-4 py-3 sm:px-3 sm:py-2 transition-colors touch-manipulation min-w-0 flex-1 sm:flex-none">
                                        <div class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-blue-600">{{ user.followers_count || 0 }}</div>
                                        <div class="text-xs sm:text-sm text-gray-500 group-hover:text-blue-600">팔로워</div>
                                    </button>
                                    <button type="button" @click="openFollowModal('following')"
                                            class="group text-center hover:bg-gray-50 rounded-lg px-4 py-3 sm:px-3 sm:py-2 transition-colors touch-manipulation min-w-0 flex-1 sm:flex-none">
                                        <div class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-blue-600">{{ user.following_count || 0 }}</div>
                                        <div class="text-xs sm:text-sm text-gray-500 group-hover:text-blue-600">팔로잉</div>
                                    </button>
                                    <div class="text-center px-4 py-3 sm:px-3 sm:py-2 min-w-0 flex-1 sm:flex-none">
                                        <div class="text-lg sm:text-xl font-bold text-gray-900">{{ user.posts_count || 0 }}</div>
                                        <div class="text-xs sm:text-sm text-gray-500">게시물</div>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <!-- Self profile actions -->
                                        <template v-if="auth && user.id == auth.id">
                                            <profile-modal-component :user="user"></profile-modal-component>
                                        </template>

                                        <!-- Other user actions -->
                                        <template v-else>
                                            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                                                <follow-toggle-button :user="user" class="flex-1 min-h-[44px] sm:min-h-[36px]"></follow-toggle-button>
                                                <button type="button"
                                                        class="px-6 py-3 sm:py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-full hover:bg-gray-50 transition-colors touch-manipulation min-h-[44px] sm:min-h-[36px] flex items-center justify-center">
                                                    언급
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- More options for other users -->
                                    <template v-if="auth && user.id != auth.id">
                                        <dropdown-component class="ml-3 sm:ml-3">
                                            <template v-slot:mybutton>
                                                <button type="button" class="p-3 sm:p-2.5 rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors touch-manipulation min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                    </svg>
                                                </button>
                                            </template>
                                            <div class="dropdown w-64 sm:w-56 bg-white border border-gray-200 rounded-2xl shadow-xl absolute right-0 top-12 z-50 py-2">
                                                <user-action-button actionable-type="user" action-name="share" :model="user" :auth="auth"
                                                                  class="w-full px-4 py-4 sm:py-3 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 flex items-center space-x-3 transition-colors touch-manipulation min-h-[48px] sm:min-h-0">
                                                    <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                                    </svg>
                                                    <span>링크복사</span>
                                                </user-action-button>

                                                <user-action-button actionable-type="user" action-name="show_profile" :model="user" :auth="auth"
                                                                  class="w-full px-4 py-4 sm:py-3 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 flex items-center space-x-3 transition-colors touch-manipulation min-h-[48px] sm:min-h-0">
                                                    <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>이 프로필 정보</span>
                                                </user-action-button>

                                                <div class="border-t border-gray-100 my-2 sm:my-1"></div>

                                                <user-action-button actionable-type="user" action-name="block" :model="user" :auth="auth"
                                                                  class="w-full px-4 py-4 sm:py-3 text-sm text-red-600 hover:bg-red-50 active:bg-red-100 flex items-center space-x-3 transition-colors touch-manipulation min-h-[48px] sm:min-h-0">
                                                    <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                                    </svg>
                                                    <span>차단하기{{ user && user.block_id ? ' 취소' : '' }}</span>
                                                </user-action-button>

                                                <user-action-button actionable-type="user" action-name="report" :model="user" :auth="auth"
                                                                  class="w-full px-4 py-4 sm:py-3 text-sm text-red-600 hover:bg-red-50 active:bg-red-100 flex items-center space-x-3 transition-colors touch-manipulation min-h-[48px] sm:min-h-0">
                                                    <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                    </svg>
                                                    <span>신고하기{{ user && user.report_id ? ' 취소' : '' }}</span>
                                                </user-action-button>
                                            </div>
                                        </dropdown-component>
                                    </template>
                                </div>

                                <!-- Follow Modal -->
                                <teleport to="body">
                                    <modal-component :is-visible="followModalShow" @close="closeFollowModal" width="500px">
                                        <div v-if="userList?.data?.length > 0" class="p-4 sm:p-6">
                                            <div class="font-bold mb-4 sm:mb-6 text-lg sm:text-xl text-center">{{followModalType}}</div>
                                            <div class="space-y-3 sm:space-y-4 max-h-80 sm:max-h-96 overflow-y-auto">
                                                <template v-for="modalUser in userList.data" :key="modalUser.id">
                                                    <div class="flex items-center justify-between p-3 sm:p-3 rounded-xl hover:bg-gray-50 active:bg-gray-100 transition-colors touch-manipulation">
                                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                                            <img :src="modalUser.profile_photo_url" :alt="modalUser.username"
                                                                 class="w-12 h-12 sm:w-12 sm:h-12 rounded-full ring-2 ring-gray-100 shrink-0">
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-semibold text-gray-900 truncate">{{ modalUser.name }}</div>
                                                                <div class="text-sm text-gray-500 truncate">@{{ modalUser.username }}</div>
                                                            </div>
                                                        </div>
                                                        <button class="px-4 py-2.5 sm:px-4 sm:py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 active:bg-blue-800 transition-colors touch-manipulation shrink-0 min-h-[44px] sm:min-h-0">
                                                            팔로우
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <div v-else class="p-6 sm:p-6 text-center">
                                            <div class="text-gray-500">
                                                <span v-html="followModalType"></span>
                                                <span>중인 사람이 없습니다.</span>
                                            </div>
                                        </div>
                                    </modal-component>
                                </teleport>
                            </div>
                        </div>

                        <!-- Activity tabs -->
                        <div class="bg-white border-b sm:border sm:rounded-2xl sm:shadow-sm overflow-hidden">
                            <div class="border-b border-gray-200">
                                <div class="flex">
                                    <template v-for="type in activityTypes" :key="type.key">
                                        <button type="button"
                                                class="flex-1 text-sm px-3 py-4 font-medium border-b-2 transition-colors touch-manipulation"
                                                :class="type.key === selectedActivityType ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                                @click="clickTab(type)">
                                            {{ type.value }}
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Activity content -->
                            <div class="min-h-[200px]">
                                <template v-if="list?.data?.length > 0">
                                    <div class="divide-y divide-gray-100" ref="sortableEl">
                                        <!-- Bookcase content -->
                                        <template v-if="selectedActivityType === 'bookcase'">
                                            <bookcase-component
                                                v-for="bookcase in list.data"
                                                :key="bookcase.id"
                                                :bookcase="bookcase"
                                                :auth="auth"
                                                class="p-4 sm:p-6">
                                            </bookcase-component>
                                        </template>

                                        <!-- Post content -->
                                        <template v-else-if="selectedActivityType === 'post'">
                                            <feed-component
                                                v-for="post in list.data"
                                                :key="post.id"
                                                :feed="post"
                                                :auth="auth">
                                            </feed-component>
                                        </template>

                                        <!-- Reply content -->
                                        <template v-else-if="selectedActivityType === 'reply'">
                                            <comment-component
                                                v-for="comment in list.data"
                                                :key="comment.id"
                                                :comment="comment"
                                                :auth="auth"
                                                class="p-4 sm:p-6">
                                            </comment-component>
                                        </template>

                                        <!-- Quotation content -->
                                        <template v-else-if="selectedActivityType === 'quotation'">
                                            <feed-component
                                                v-for="quotation in list.data"
                                                :key="quotation.id"
                                                :feed="quotation"
                                                :auth="auth">
                                            </feed-component>
                                        </template>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <template v-else>
                                    <div class="p-8 sm:p-12 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">아직 콘텐츠가 없습니다</h3>
                                        <p class="text-gray-500">첫 번째 {{ activityTypes.find(t => t.key === selectedActivityType)?.value }}을 만들어보세요!</p>
                                    </div>
                                </template>

                                <!-- Loading indicator -->
                                <template v-if="loading">
                                    <div class="p-8 text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                                        <p class="text-sm text-gray-500 mt-2">로딩 중...</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Mobile optimizations */
@media (max-width: 640px) {
    .touch-manipulation {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    /* Improve touch targets */
    .touch-manipulation {
        min-height: 44px;
        min-width: 44px;
    }

    /* Better spacing for mobile */
    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }

    /* Mobile dropdown adjustments */
    .dropdown {
        max-width: calc(100vw - 2rem);
        right: 1rem !important;
    }
}

/* Enhanced transitions */
.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.transition-shadow {
    transition: box-shadow 0.3s ease;
}

/* Cover gradient animation */
.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradient 8s ease infinite;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Profile image hover effect */
.profile-image {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.profile-image:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Button interactions */
.hover\\:scale-105:hover {
    transform: scale(1.05);
}

/* Improve scrolling */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #CBD5E0;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #A0AEC0;
}
</style>
