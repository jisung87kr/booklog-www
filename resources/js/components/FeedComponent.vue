<script setup>
import { ref, onMounted, onBeforeUnmount, defineEmits } from 'vue';
import {wrapWithSpan} from "../common.js";
import {useUserStore} from "../stores/user.js";
import BookcaseComponent from "./BookcaseComponent.vue";

const userStore = useUserStore();
const auth = ref(userStore.user);
const props = defineProps({
    feed: {
        type: Object,
        required: true,
    },
});
</script>
<template>
    <div>
        <div class="w-full">
            <div class="flex justify-between">
                <div class="flex">
                    <div class="profile shrink-0 mr-3">
                        <img class="w-8 h-8 rounded-full" :src="feed.user.profile_photo_url">
                    </div>
                    <div class="mr-3 font-bold">
                        <a :href="'/@'+feed.user.username">{{ feed.user.username }}</a>
                    </div>
                    <div class="opacity-75" v-html="feed.created_at_human"></div>
                </div>
                <dropdown-component v-if="auth">
                    <template v-slot:mybutton>
                        <button type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots" width="16"
                                 height="16"
                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"/>
                            </svg>
                        </button>
                    </template>

                    <ul class="dropdown w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10">
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200 rounded-t-lg">
                            <user-action-button actionable-type="post" action-name="bookmark" :model="feed" :auth="auth">
                                <span>저장</span><span v-if="feed && feed.bookmark_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200">
                            <user-action-button actionable-type="post" action-name="uninterested" :model="feed" :auth="auth">
                                <span>관심없음</span><span v-if="feed && feed.uninterested_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200">
                            <user-action-button actionable-type="post" action-name="block" :model="feed" :auth="auth">
                                <span>차단하기</span><span v-if="feed && feed.block_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200" v-if="auth.id !== feed.user.id">
                            <user-action-button actionable-type="post" action-name="report" :model="feed" :auth="auth">
                                <span>신고하기</span><span v-if="feed && feed.report_id"> 취소</span>
                            </user-action-button>
                        </li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 rounded-b-lg">
                            <user-action-button actionable-type="post" action-name="share" :model="feed" :auth="auth">
                                <span>링크복사</span>
                            </user-action-button>
                        </li>
                    </ul>
                </dropdown-component>
            </div>
            <template v-if="feed.type === 'bookcase'">
                <bookcase-component :bookcase="feed.bookcase" class="mt-1 pl-10" sortable="false"></bookcase-component>
            </template>
            <template v-else>
                <div class="mt-1 pl-10">
                    <div class="mb-1 text-gray-600 feed-content" v-html="wrapWithSpan(feed.content)"></div>
                    <template v-if="feed.images.length > 0">
                        <swiper-component :images="feed.images" class="mt-3"></swiper-component>
                    </template>
                    <div class="mt-3 flex gap-3">
                        <like-button :model="feed" :auth="auth" type="post"></like-button>
                        <comment-button :model="feed" type="post"></comment-button>
                        <share-button :model="feed" type="post"></share-button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<style scoped>
.feed-content .tagbox{
    background: royalblue;
}
</style>
