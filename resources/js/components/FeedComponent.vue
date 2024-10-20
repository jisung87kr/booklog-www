<template>
    <div class="">
        <div class="w-full">
            <div class="flex justify-between">
                <div class="flex">
                    <div class="profile shrink-0 mr-3">
                        <img class="w-8 h-8 rounded-full" :src="feed.user.profile_photo_url" alt="Neil image">
                    </div>
                    <div class="mr-3 font-bold">{{ feed.user.name }}</div>
                    <div class="opacity-75" v-html="feed.created_at_human"></div>
                </div>
                <dropdown-component>
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
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200 rounded-t-lg"><a href="">저장</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">관심 없음</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">차단하기</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">신고하기</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 rounded-b-lg"><a href="">링크복사</a></li>
                    </ul>
                </dropdown-component>
            </div>
            <div class="mt-2 pl-10">
                <div class="mb-1" v-html="feed.note"></div>
                <template v-if="feed.images.length > 0">
                    <swiper-component :images="feed.images"></swiper-component>
                </template>
                <div class="mt-3 flex gap-3">
                    <button type="button" @click="toggleLike(feed)">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="icon icon-tabler icon-tabler-heart"
                             width="20"
                             height="20"
                             viewBox="0 0 24 24" stroke-width="1.5" :stroke="feed.like_id ? 'red' : '#000000'" :fill="feed.like_id ? 'red' : 'none' " stroke-linecap="round"
                             stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"/>
                        </svg>
                    </button>
                    <button type="button" @click="showContent(feed)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2"
                             width="20"
                             height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1"/>
                        </svg>
                    </button>
                    <button type="button" @click="copyUrl(feed)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="20"
                             height="20"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 14l11 -11"/>
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import dropdownComponent from "./DropdownComponent.vue";
import swiperComponent from "./SwiperComponent.vue";
import {sendRequest} from '../common.js';
export default {
    name: 'FeedComponent',
    components:{
        dropdownComponent,
        swiperComponent,
    },
    props: {
        feed: {
            type: Object,
            required: true
        },
        user: {
            type: Object,
            required: false,
        }
    },
    mounted() {

    },
    methods:{
        toggleDropdown() {
            this.$refs.dropdownComponent.toggleDropdown();
        },
        toggleModal(){
            this.modalOpen = !this.modalOpen;
        },
        async toggleLike(feed){
            if(feed.like_id){
                await sendRequest('delete', `/api/users/${this.user.id}/actions/${feed.like_id}`);
                feed.like_id = null;
            } else {
                let data = {
                    'action': 'reading_process_like',
                    'user_actionable_id': feed.id,
                    'user_actionable_type': 'processes',
                }
                let result = await sendRequest('post', `/api/users/${this.user.id}/actions`, data);
                feed.like_id = result.data.id;
            }
        },
        showContent(feed){
            this.contentModalOpen = true;
        },
        copyUrl(feed){
            // 변수 url를 클립보드에 복사하는 기능 textarea를 생성하여 복사하는 방법
            const url = window.location.origin + '/feeds/' + feed.id;
            const textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('URL이 복사되었습니다.');
        }
    }
};
</script>
