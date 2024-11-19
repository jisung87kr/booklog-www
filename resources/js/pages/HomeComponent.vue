<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import {useUserStore} from "../stores/user.js";

const userStore = useUserStore();
await userStore.checkUser();
const auth = ref(userStore.user);

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

const fetchFeeds = async (page = 1) => {
    try {
        loading.value = true;
        let response = await axios({
            method: "get",
            url: `/api/feeds?page=${page}`,
        });

        if (response.data.status !== true) {
            throw new Error(response.data.message);
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

const showContentModal = (feed) => {
    contentModalOpen.value = true;
    selectedFeed.value = feed;
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

onMounted(async () => {
    window.addEventListener("scroll", handleScroll);
    //auth.value = await fetchUser();
    const feedsResponse = await fetchFeeds();
    feeds.value = feedsResponse.data;
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                <feed-component :feed="feed"
                                v-for="feed in feeds.data"
                                :key="feed.id"
                                :auth="auth"
                                class="p-4"
                                @open-comment-modal="showContentModal"
                ></feed-component>
            </div>
            <div class="max-w-lg w-full mx-6 hidden lg:block">
                <div class="flex flex-col gap-6">
                    <div class="bg-white border rounded-2xl p-6">
                        <div>나를 위한 트랜드</div>
                    </div>
                    <div class="bg-white border rounded-2xl p-6">
                        <div>팔로우 추천</div>
                    </div>
                </div>
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
                                      :auth="auth"
                                      @stored-comment="scrollBottom"
                        ></comment-form>
                    </div>
                </div>
            </template>
        </modal-component>
    </div>
</template>
