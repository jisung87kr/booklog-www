<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useUserStore } from "../stores/user.js";
import DropdownComponent from "../components/DropdownComponent.vue";
import HeaderComponent from "../components/headerComponent.vue";

// 사용자 인증 스토어 사용 설정
const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const loaded = ref(false);
const selectedActivityType = ref('follow');
const activityTypes = [
    { key: 'follow', value: '팔로우' },
    { key: 'reply', value: '답글' },
    { key: 'mention', value: '언급' },
    { key: 'quotation', value: '인용' },
];

const q = ref(new URLSearchParams(window.location.search).get('q') || '');
const qsearch_type = ref(new URLSearchParams(window.location.search).get('qsearch_type') || '');
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

const fetchFollowers = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/followers`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchReplies = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/replies`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchMentions = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/mentions`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchQuotation = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/quotations`);
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

const clickTab = (activityType) => {
    selectedActivityType.value = activityType.key;
    getList(1);
};

const getList = async (page) => {
    if (page === 1) {
        list.value.data = [];
    }

    let response = {};
    switch (selectedActivityType.value) {
        case 'follow':
            response = await fetchFollowers();
            break;
        case 'reply':
            response = await fetchReplies();
            break;
        case 'mention':
            response = await fetchMentions();
            break;
        case 'quotation':
            response = await fetchQuotation();
            break;
    }

    list.value.data = [...list.value.data, ...response.data.data];
    list.value.current_page = response.data.current_page;
    list.value.last_page = response.data.last_page;
    list.value.total = response.data.total;
};

onMounted(async () => {
    window.addEventListener("scroll", handleScroll);
    await getList(1);
    loaded.value = true;
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <transition name="slide-fade">
        <div v-if="loaded">
            <header-component>
                <div class="flex justify-between w-full">
                    <history-back-button></history-back-button>
                    <div class="flex items-center">
                        <div class="font-bold">활동</div>
                        <dropdown-component class="z-30">
                            <template #mybutton>
                                <button type="button" class="rounded-full border w-7 h-7 bg-gray-50 ms-3 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2" class="inline-block"> <path d="M6 9l6 6l6 -6"></path> </svg>
                                </button>
                            </template>
                            <ul class="absolute left-1/2 -translate-x-1/2 bottom-[-210px] w-[200px] border rounded-xl bg-white px-2 py-3">
                                <template v-for="(activityType, idx) in activityTypes"
                                          :key="idx"
                                >
                                    <li>
                                        <button type="button"
                                                class="px-4 py-2.5 hover:bg-gray-100 flex justify-between w-full"
                                                @click="clickTab(activityType);"
                                        >
                                            <div>{{activityType.value}}</div>
                                            <div v-if="selectedActivityType == activityType.key">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"> <path d="M5 12l5 5l10 -10"></path> </svg>
                                            </div>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </dropdown-component>
                    </div>
                    <div></div>
                </div>
            </header-component>
            <div class="container-fluid mx-auto w-full">
                <div class="flex justify-center">
                    <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full shadow">
                        <template v-if="list.data.length > 0">
                            <template v-if="selectedActivityType == 'follow'">
                                <follower-component v-for="follow in list.data"
                                                    :key="follow.id"
                                                    :follow="follow"
                                                    :auth="auth"
                                                    class="p-4"
                                ></follower-component>
                            </template>
                            <template v-else-if="selectedActivityType == 'reply'">
                                <comment-component v-for="comment in list.data"
                                                   :key="comment.id"
                                                   :comment="comment"
                                                   :auth="auth"
                                                   :feed="comment.commentable"
                                                   class="p-4"
                                >
                                </comment-component>
                            </template>
                            <template v-else-if="selectedActivityType == 'mention'">
                                <feed-component v-for="mention in list.data"
                                                :key="mention.id"
                                                :auth="auth"
                                                class="p-4"
                                                :feed="mention.post"
                                ></feed-component>
                            </template>
                            <template v-else-if="selectedActivityType == 'quotation'">
                                <feed-component v-for="quotation in list.data"
                                                :key="quotation.id"
                                                :auth="auth"
                                                class="p-4"
                                                :feed="quotation"
                                ></feed-component>
                            </template>
                        </template>
                        <template v-else>
                            <div class="p-6 text-gray-500 text-sm font-bold">데이터가 존재하지 않습니다.</div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
