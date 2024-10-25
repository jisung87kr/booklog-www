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
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200 rounded-t-lg"><a href="">저장</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">관심 없음</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">차단하기</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">신고하기</a></li>
                        <li class="w-full px-4 py-2 hover:bg-indigo-50 rounded-b-lg"><a href="">링크복사</a></li>
                    </ul>
                </dropdown-component>
            </div>
            <div class="mt-1 pl-10">
                <div class="mb-1 text-gray-600" v-html="feed.note"></div>
                <template v-if="feed.images.length > 0">
                    <swiper-component :images="feed.images" class="mt-3"></swiper-component>
                </template>
                <div class="mt-3 flex gap-3">
                    <like-button :model="feed" :auth="auth"></like-button>
                    <comment-button :model="feed" @showComment="showComment"></comment-button>
                    <share-button :feed="feed" ></share-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import dropdownComponent from "./DropdownComponent.vue";
import swiperComponent from "./SwiperComponent.vue";
import likeButton from "./buttons/LikeButton.vue";
import CommentButton from "./buttons/CommentButton.vue";
import ShareButton from "./buttons/ShareButton.vue";
export default {
    name: 'FeedComponent',
    components:{
        dropdownComponent,
        swiperComponent,
        likeButton,
        CommentButton,
        ShareButton,
    },
    props: {
        feed: {
            type: Object,
            required: true
        },
        auth: {
            type: Object,
            required: false,
        },
        showContentModal: {
            type: Function,
            required: false,
        }
    },
    mounted() {

    },
    methods:{
        toggleModal(){
            this.modalOpen = !this.modalOpen;
        },
        showComment(model){
            this.$emit('openCommentModal', model);
        },
    }
};
</script>
