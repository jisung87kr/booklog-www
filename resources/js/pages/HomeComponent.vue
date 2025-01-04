<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import {useUserStore} from "../stores/user.js";
import {usePostFormStore} from "../stores/postForm.js";
import CommentModalComponent from "../components/CommentModalComponent.vue";
import {sendRequest} from '../common.js';
import AvatarComponent from "../components/AvatarComponent.vue";

const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const loaded = ref(false);
const postFormStore = usePostFormStore();
const recommendedUsers = ref(null);
postFormStore.$onAction(
    async ({
         name, // action 이름.
         store, // Store 인스턴스, `someStore`와 같음.
         args, // action으로 전달된 매개변수로 이루어진 배열.
         after, // action에서 return 또는 resolve 이후의 훅.
         onError, // action에서 throw 또는 reject 될 경우의 훅.
     }) => {
        if(name === 'createPost'){
            after(async () => {
                alert('게시되었습니다.');
                modalOpen.value = false;
                feeds.value = await fetchFeeds();
            });
        }
    }
)

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

        return response.data.data;
    } catch (error) {
        alert(error.message);
    } finally {
        loading.value = false;
    }
};

const fetchRecommendedUsers = async () => {
    return await sendRequest('GET', '/api/recommend/users');
};

const handleScroll = async () => {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight * 0.8 && !loading.value && feeds.value.current_page < feeds.value.last_page) {
        const nextPage = feeds.value.current_page + 1;
        const feedsResponse = await fetchFeeds(nextPage);

        feeds.value.data = [...feeds.value.data, ...feedsResponse.data];
        feeds.value.current_page = feedsResponse.data.current_page;
        feeds.value.last_page = feedsResponse.data.last_page;
        feeds.value.total = feedsResponse.data.total;
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

onMounted(async () => {
    window.addEventListener("scroll", handleScroll);
    feeds.value = await fetchFeeds();
    const response = await fetchRecommendedUsers();
    recommendedUsers.value = response.data;
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
                <div class="font-bold">회원님을 위한 추천</div>
            </header-component>
            <div class="container-fluid mx-auto w-full">
                <div class="flex justify-center">
                    <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                        <feed-component :feed="feed"
                                        v-for="feed in feeds.data"
                                        :key="feed.id"
                                        :auth="auth"
                                        class="p-4"
                        ></feed-component>
                    </div>
                    <div class="max-w-lg w-full mx-6 hidden lg:block">
                        <div class="flex flex-col gap-6">
                            <div class="bg-white border rounded-2xl p-6">
                                <div>나를 위한 트랜드</div>
                            </div>
                            <div class="bg-white border rounded-2xl p-6 pb-0" v-if="recommendedUsers.length > 0">
                                <div>팔로우 추천</div>
                                <div class="divide-y">
                                    <template v-for="user in recommendedUsers" :key="user.id">
                                        <avatar-component :user="user" class="py-4"></avatar-component>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
