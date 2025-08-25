<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useUserStore } from '../stores/user';
import {sendRequest} from '../common.js';
import debounce from 'lodash/throttle';
import HeaderComponent from "../components/headerComponent.vue";
import AvatarComponent from "../components/AvatarComponent.vue";
// 사용자 인증 스토어 사용 설정
const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const loaded = ref(false);

const feeds = ref({
    current_page: 1,
    data: [],
    last_page: null,
    total: null,
});
const loading = ref(false);
const modalOpen = ref(false);
const contentModalOpen = ref(false);
const q = ref(new URLSearchParams(window.location.search).get('q') || '');
const qsearch_type = ref(new URLSearchParams(window.location.search).get('qsearch_type') || null);
const recommendedUsers = ref([]);
const searchData = ref([]);
const bookData = ref([]);

const fetchFeeds = async (type, page = 1) => {
    try {
        loading.value = true;
        let params = {
            page: page,
            q: q.value,
            qsearch_type: type,
        };

        let response = await sendRequest('GET', '/api/feeds', params);

        if (response.status !== true) {
            throw new Error(response.message);
        }

        // if(qsearch_type.value === 'book'){
        //     bookData.value = response.data.book;
        //     return response.data.feeds;
        // }

        return response.data;
    } catch (error) {
        alert(error.message);
    } finally {
        loading.value = false;
    }
};

const handleScroll = async () => {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight * 0.8 && !loading.value && feeds.value.current_page < feeds.value.last_page) {
        const nextPage = feeds.value.current_page + 1;
        const feedsResponse = await fetchFeeds('default', nextPage);

        feeds.value.data = [...feeds.value.data, ...feedsResponse.data.data];
        feeds.value.current_page = feedsResponse.data.current_page;
        feeds.value.last_page = feedsResponse.data.last_page;
        feeds.value.total = feedsResponse.data.total;
    }
};

const fetchRecommendedUsers = async () => {
    loading.value = true;
    const response = await axios.get('/api/recommend/users');
    recommendedUsers.value = response.data;
    loading.value = false;
};

const throttleEvent = debounce(async (callback) => {
    await callback();
}, 1000);


const search = async () => {
    throttleEvent(async () => {
        // feeds.value = await fetchFeeds();
        // feeds.value = feedsResponse;
        searchData.value = await fetchSearchData();
    });
};

const fetchSearchData = async () => {
    return await sendRequest('GET', 'api/search', {q: q.value, qsearch_type: qsearch_type.value});
}

onMounted(async () => {
    await fetchRecommendedUsers();
    await fetchSearchData();
    window.addEventListener("scroll", handleScroll);
    if (q.value) {
        const feedsResponse = await fetchFeeds('default');
        feeds.value = feedsResponse;
        const bookResponse = await fetchFeeds('book');
        if (bookResponse) {
            bookData.value = bookResponse.book;
        }
    }

    loaded.value = true;
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});

const submitSearch = () => {
    if (q.value.trim() === '') {
        alert('검색어를 입력해주세요.');
        return;
    }
    if (qsearch_type.value === null) {
        qsearch_type.value = 'default';
    }
    window.location.href = `/search?q=${encodeURIComponent(q.value)}&qsearch_type=${qsearch_type.value}`;
};

</script>
<template>
    <Transition name="slide-fade">
        <div v-if="loaded" class="min-h-screen bg-gray-50">
            <!-- Clean and simple header -->
            <header-component class="sticky top-0 z-30 bg-white border-b border-gray-200 !max-w-full px-2 md:px-0 mb-6">
                <div class="flex justify-between items-center max-w-2xl w-full">
                    <history-back-button class="touch-manipulation"></history-back-button>
                    <div class="font-bold text-gray-900">검색</div>
                    <div class="w-6"></div> <!-- Spacer for balance -->
                </div>
            </header-component>

            <div class="container-fluid mx-auto w-full px-0 sm:px-4 pb-20 sm:pb-8">
                <div class="flex justify-center">
                    <!-- Modern search container -->
                    <div class="w-full max-w-2xl">
                        <!-- Enhanced search form -->
                        <div class="bg-white border-b sm:border sm:rounded-2xl sm:shadow-sm mb-0 sm:mb-6">
                            <form @submit.prevent="submitSearch" class="p-4 sm:p-6">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        name="q"
                                        v-model="q"
                                        @keyup="search"
                                        class="block w-full pl-12 pr-4 py-3 sm:py-4 text-sm sm:text-base border border-gray-300 rounded-2xl bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 touch-manipulation"
                                        placeholder="책, 사람, 내용을 검색하세요..."
                                        autocomplete="off">
                                    <input type="hidden" name="qsearch_type" v-model="qsearch_type">

                                    <!-- Clear button -->
                                    <button
                                        v-if="q.length > 0"
                                        type="button"
                                        @click="q = ''; search()"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center touch-manipulation">
                                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Search results container -->
                        <div class="bg-white border-b sm:border sm:rounded-2xl sm:shadow-sm overflow-hidden">
                            <!-- Default state - Follow recommendations and recent searches -->
                            <template v-if="qsearch_type == null">
                                <template v-if="q === ''">
                                    <!-- Follow recommendations section -->
                                    <div class="p-4 sm:p-6">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <h3 class="text-lg font-semibold text-gray-900">팔로우 추천</h3>
                                        </div>

                                        <div class="space-y-4" v-if="recommendedUsers?.data?.length > 0">
                                            <template v-for="user in recommendedUsers.data" :key="user.id">
                                                <avatar-component :user="user" class="py-4">
                                                    <template v-slot:follower-count>
                                                        <div class="mt-3 text-sm">팔로워 <span v-html="user.followers_count"></span>명</div>
                                                    </template>
                                                </avatar-component>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Search suggestions -->
                                <template v-else>
                                    <div class="divide-y divide-gray-100">
                                        <!-- Related keywords -->
                                        <template v-if="searchData?.data?.relatedKeywords?.length > 0">
                                            <div class="p-4 sm:p-6">
                                                <div class="flex items-center space-x-2 mb-4">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                    <h3 class="text-lg font-semibold text-gray-900">추천 검색어</h3>
                                                </div>

                                                <div class="space-y-2">
                                                    <template v-for="(keyword, idx) in searchData.data.relatedKeywords" :key="idx">
                                                        <a :href="'/search?q='+keyword+'&qsearch_type=default'"
                                                           class="flex items-center space-x-3 p-3 rounded-xl hover:bg-blue-50 group transition-colors touch-manipulation">
                                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                            </svg>
                                                            <span class="flex-1 font-medium text-gray-900 group-hover:text-blue-700" v-html="keyword"></span>
                                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                            </svg>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- User results -->
                                        <template v-if="searchData?.data?.users?.length > 0">
                                            <div class="p-4 sm:p-6">
                                                <div class="flex items-center space-x-2 mb-4">
                                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <h3 class="text-lg font-semibold text-gray-900">사용자</h3>
                                                </div>

                                                <div class="space-y-3">
                                                    <template v-for="user in searchData.data.users" :key="user.id">
                                                        <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors">
                                                            <div class="flex items-center space-x-3">
                                                                <img :src="user.profile_photo_url" :alt="user.username"
                                                                     class="w-10 h-10 rounded-full ring-2 ring-gray-100">
                                                                <div>
                                                                    <div class="font-semibold text-gray-900">{{ user.name }}</div>
                                                                    <div class="text-sm text-gray-500">@{{ user.username }}</div>
                                                                </div>
                                                            </div>
                                                            <button class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-full hover:bg-gray-200 transition-colors touch-manipulation">
                                                                보기
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </template>

                            <!-- Search results -->
                            <template v-else>
                                <div class="divide-y divide-gray-100">
                                    <!-- Book result -->
                                    <template v-if="bookData && bookData.id">
                                        <div class="p-4 sm:p-6">
                                            <div class="flex items-center space-x-2 mb-4">
                                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                                <h3 class="text-lg font-semibold text-gray-900">도서</h3>
                                            </div>

                                            <div class="flex items-start space-x-4 p-4 rounded-2xl bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200">
                                                <div class="shrink-0">
                                                    <a :href="bookData.link" target="_blank" class="block rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                                        <img :src="bookData.cover_image" :alt="bookData.title" class="w-20 sm:w-24 h-auto">
                                                    </a>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <a :href="bookData.link" target="_blank" class="group">
                                                        <h4 class="font-bold text-gray-900 line-clamp-2 group-hover:text-orange-700 transition-colors">{{ bookData.title }}</h4>
                                                        <p class="text-sm text-gray-600 mt-1">{{ bookData.publisher }} / {{ bookData.author }}</p>
                                                        <p class="text-sm text-gray-700 mt-2 line-clamp-3" v-html="bookData.description"></p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Feed results -->
                                    <template v-if="feeds?.data?.length > 0">
                                        <div class="space-y-0">
                                            <feed-component
                                                v-for="feed in feeds.data"
                                                :key="feed.id"
                                                :feed="feed"
                                                :auth="auth">
                                            </feed-component>
                                        </div>
                                    </template>

                                    <!-- No results -->
                                    <template v-else-if="q.length > 0">
                                        <div class="p-8 sm:p-12 text-center">
                                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">검색 결과가 없습니다</h3>
                                            <p class="text-gray-500">다른 검색어를 시도해보세요.</p>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- Loading indicator -->
                            <template v-if="loading">
                                <div class="p-8 text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                                    <p class="text-sm text-gray-500 mt-2">검색 중...</p>
                                </div>
                            </template>
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
}

/* Search input focus styles */
.search-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
