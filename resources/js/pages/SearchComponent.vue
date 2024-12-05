<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useUserStore } from '../stores/user';
import {sendRequest} from '../common.js';
import throttle from 'lodash/throttle';
import HeaderComponent from "../components/headerComponent.vue";
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
const selectedFeed = ref({
    id: null,
    user: {
        name: null,
        profile_photo_url: null,
    },
    note: null,
    images: [],
});

const q = ref(new URLSearchParams(window.location.search).get('q') || '');
const qsearch_type = ref(new URLSearchParams(window.location.search).get('qsearch_type') || '');
const recommendedUsers = ref([]);

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

const throttleEvent = throttle(async (callback) => {
    await callback();
}, 1000);


const search = async () => {
    throttleEvent(async () => {
        const feedsResponse = await fetchFeeds();
        feeds.value = feedsResponse;
    });
};

onMounted(async () => {
    await fetchRecommendedUsers();
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
                <div class="font-bold">검색</div>
            </header-component>
            <div class="container-fluid mx-auto w-full">
                <div class="flex justify-center">
                    <div class="bg-white sm:border sm:rounded-2xl flex-start max-w-xl w-full">
                        <form action="" class="p-6">
                            <div class="relative pl-6 border rounded-lg w-full">
                                <button type="button" class="absolute left-4 top-2.5 -translate-x-1/2 opacity-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                </button>
                                <input type="text" name="q" class="w-full border-none focus:ring-transparent" placeholder="검색" v-model="q" @keyup="search">
                                <input type="hidden" name="qsearch_type" class="border-none w-full focus:border-none" placeholder="검색" v-model="qsearch_type">
                            </div>
                        </form>
                        <template v-if="q == ''">
                            <div class="opacity-60 font-medium px-6">팔로우 추천</div>
                            <div class="divide-y">
                                <template v-for="user in recommendedUsers.data" :key="user.id">
                                    <avatar-component :user="user" class="p-4">
                                        <template v-slot:follower-count>
                                            <div class="mt-3 text-sm">팔로워 <span v-html="user.followers_count"></span>명</div>
                                        </template>
                                    </avatar-component>
                                </template>
                            </div>
                        </template>
                        <template v-else>
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
