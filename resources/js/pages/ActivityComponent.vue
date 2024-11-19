<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useUserStore } from "../stores/user.js";

// 사용자 인증 스토어 사용 설정
const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);

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

const showContentModal = (feed) => {
    contentModalOpen.value = true;
    selectedFeed.value = feed;
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

const scrollBottom = () => {
    nextTick(() => {
        const modalContent = document.querySelector(".modal-body");
        modalContent.scrollTo({
            top: modalContent.scrollHeight,
            behavior: "smooth",
        });
    });
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
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center mt-3 md:mt-0">
            <div class="overflow-x-auto">
                <div class="flex flex-nowrap gap-3">
                    <template v-for="(activityType, idx) in activityTypes"
                              :key="idx"
                    >
                        <button type="button"
                                class="shrink-0 px-3.5 py-2 border rounded-lg font-medium hover:bg-gray-200"
                                :class="selectedActivityType == activityType.key ? 'bg-gray-200' : ''"
                                @click="clickTab(activityType)"
                        >{{activityType.value}}</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-3">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6 shadow">
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
                                        class-name="p-4"
                                        :feed="mention.post"
                                        @open-comment-modal="showContentModal"
                        ></feed-component>
                    </template>
                    <template v-else-if="selectedActivityType == 'quotation'">
                        <feed-component v-for="quotation in list.data"
                                        :key="quotation.id"
                                        :auth="auth"
                                        class-name="p-4"
                                        :feed="quotation"
                                        @open-comment-modal="showContentModal"
                        ></feed-component>
                    </template>
                </template>
                <template v-else>
                    <div class="p-6 text-gray-500 text-sm font-bold">데이터가 존재하지 않습니다.</div>
                </template>
            </div>
        </div>
        <modal-component :is-visible="contentModalOpen"
                         @close="contentModalOpen = false"
        >
            <template v-slot:modal-header>
                <div class="p-3">
                    <div class="mb-3 font-bold">댓글</div>
                </div>
            </template>
            <div class="p-3">
                <div>
                    <comment-list :model="selectedFeed"
                                  :auth="auth"
                    ></comment-list>
                </div>
            </div>
            <template v-slot:modal-footer>
                <div class="p-3 border-t">
                    <div class="flex gap-2">
                        <like-button :auth="auth" :model="selectedFeed"></like-button>
                        <share-button :feed="selectedFeed"></share-button>
                    </div>
                    <div class="mt-1">
                        <div class="text-sm">좋아요 400개</div>
                    </div>
                    <div class="mt-3" v-if="auth">
                        <comment-form :model="selectedFeed"
                                      @stored-comment="scrollBottom"
                        ></comment-form>
                    </div>
                </div>
            </template>
        </modal-component>
    </div>
</template>
