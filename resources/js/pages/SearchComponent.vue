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

const fetchFeeds = async (page = 1) => {
    try {
        loading.value = true;
        let params = {
            page: page,
            q: q.value,
            qsearch_type: qsearch_type.value,
        };

        let response = await sendRequest('GET', '/api/feeds', params);

        if (response.status !== true) {
            throw new Error(response.message);
        }

        if(qsearch_type.value === 'book'){
            bookData.value = response.data.book;
            return response.data.feeds;
        }

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
        const feedsResponse = await fetchFeeds(nextPage);

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
        const feedsResponse = await fetchFeeds();
        feeds.value = feedsResponse;
    }

    loaded.value = true;
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});

</script>
<template>
    <Transition name="slide-fade">
        <div v-if="loaded">
            <header-component>
                <div class="flex justify-between w-full">
                    <history-back-button></history-back-button>
                    <div class="font-bold">검색</div>
                    <div></div>
                </div>
            </header-component>
            <div class="container-fluid mx-auto w-full">
                <div class="flex justify-center">
                    <div class="bg-white sm:border sm:rounded-2xl flex-start max-w-xl w-full">
                        <form action="" class="p-6">
                            <div class="relative pl-7 border rounded-2xl w-full overflow-hidden">
                                <button type="button" class="absolute left-6 top-2.5 -translate-x-1/2 opacity-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="18" height="18" viewBox="0 0 24 24" stroke-width="1" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                </button>
                                <input type="text" name="q" class="w-full border-none focus:ring-transparent" placeholder="검색" v-model="q" @keyup="search">
                                <input type="hidden" name="qsearch_type" class="border-none w-full focus:border-none" placeholder="검색" v-model="qsearch_type">
                            </div>
                        </form>
                        <!-- 팔로우 추천 리스트 -->
                        <template v-if="qsearch_type == null">
                            <template v-if="q == ''">
                                <div class="opacity-60 font-medium px-6">팔로우 추천</div>
                                <template v-for="user in recommendedUsers.data" :key="user.id">
                                    <div class="px-6">
                                        <avatar-component :user="user" class="border-b py-4">
                                            <template v-slot:follower-count>
                                                <div class="mt-3 text-sm">팔로워 <span v-html="user.followers_count"></span>명</div>
                                            </template>
                                        </avatar-component>
                                    </div>
                                </template>
                            </template>
                            <!-- 검색어 결과 리스트 -->
                            <template v-else>
                                <template v-if="searchData.data.relatedKeywords.length > 0">
                                    <template v-for="(keyword, idx) in searchData.data.relatedKeywords" :key="idx">
                                        <div class="px-6">
                                            <a :href="'/search?q='+keyword+'&qsearch_type=default'" class="block relative px-8 border-b py-4 line-clamp-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search absolute left-0 top-1/2 -translate-y-1/2 opacity-50"
                                                     width="18" height="18" viewBox="0 0 24 24" stroke-width="1" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                    <path d="M21 21l-6 -6" />
                                                </svg>
                                                <div class="text-lg font-bold line-clamp-1" v-html="keyword"></div>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-linecap="round"
                                                     stroke-linejoin="round" width="18" height="18" stroke-width="1"
                                                     class="opacity-50 absolute right-0 top-1/2 -translate-y-1/2">
                                                    <path d="M9 6l6 6l-6 6"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </template>
                                </template>
                                <template v-if="searchData.data.users.length > 0">
                                    <template v-for="user in searchData.data.users" :key="user.id">
                                        <div class="px-6">
                                            <div class="border-b py-4">
                                                <avatar-component :user="user"></avatar-component>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </template>
                        </template>
                        <!-- 검색 결과 리스트 -->
                        <template v-else>
                            <div v-if="bookData.id"
                                 class="flex items-start p-6">
                                <div class="shrink-0 mr-3">
                                    <a :href="bookData.link" target="_blank" class="border rounded-xl block w-[130px] overflow-hidden">
                                        <img :src="bookData.cover_image" alt="" class="">
                                    </a>
                                </div>
                                <div>
                                    <a :href="bookData.link" target="_blank">
                                        <div class="font-bold line-clamp-2 break-keep">{{bookData.title}}</div>
                                        <div class="text-gray-500 text-xs break-keep">{{bookData.publisher}} / {{bookData.author}}</div>
                                        <div class="mt-1 break-keep text-gray-700" v-html="bookData.description"></div>
                                    </a>
                                </div>
                            </div>
                            <template v-if="feeds.data.length > 0">
                                <feed-component v-for="feed in feeds.data"
                                                :feed="feed"
                                                :key="feed.id"
                                                class="p-4"
                                ></feed-component>
                            </template>
                            <template v-else>
                                <div class="p-4 pt-0">검색 결과가 없습니다.</div>
                            </template>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
