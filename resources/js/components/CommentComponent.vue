<template>
    <div class="relative">
        <div class="flex justify-between">
            <div class="flex">
                <div class="profile shrink-0 mr-3">
                    <img class="w-8 h-8 rounded-full" :src="comment.user.profile_photo_url" alt="Neil image">
                </div>
                <div class="mr-3 font-bold">{{ comment.user.name }}</div>
                <div class="opacity-75" v-html="comment.created_at_human"></div>
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
                    <li>
                        <button type="button"
                                class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200" v-if="auth && comment.user_id === auth.id"
                                @click="removeComment(comment)"
                        >삭제</button>
                    </li>
                    <li v-if="auth && comment.user_id !== auth.id">
                        <button type="button"
                                class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                @click="reportComment(comment)"
                        >신고하기</button>
                    </li>
                </ul>
            </dropdown-component>
        </div>
        <div class="ps-10" v-html="comment.body"></div>
        <slot name="feed" v-if="feed">
            <div class="ps-10">
                <div class="border rounded-lg p-4 mt-3">
                    <feed-component :feed="feed"
                                    :auth="auth"
                                    @open-comment-modal="showContentModal"
                    ></feed-component>
                </div>
            </div>
        </slot>
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
                        <div class="text-sm">좋아요 {{selectedFeed.like_cnt}}개</div>
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
<script>
    import DropdownComponent from './DropdownComponent.vue';
    import FeedComponent from "./FeedComponent.vue";
    import {sendRequest} from "../common";
    export default {
    name: 'CommentComponent',
    components: {
        DropdownComponent,
        FeedComponent,
    },
    props: {
        model: {
          type: Object,
          required: false,
        },
        comment: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: false,
        },
        feed: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
    data() {
        return {
            loading: false,
            modalOpen: false,
            contentModalOpen: false,
            selectedFeed: {
                id: null,
                user: {
                    name: null,
                    profile_photo_url: null,
                },
                note: null,
                images: [],
            },
        }
    },
    mounted(){

    },
    methods: {
        async removeComment(comment) {
            const response = await sendRequest("DELETE", `api/comments/${comment.id}`);
            alert('삭제되었습니다.');
            this.$emit('removeComment', this.comment);
        },
        async reportComment() {
            let data = {
                'action': 'comment_report',
                'user_actionable_id': this.model.id,
                'user_actionable_type': 'processes',
            }
            let result = await sendRequest('POST', `/api/users/${this.auth.id}/actions`, data);
            alert('신고되었습니다.');
            this.$emit('reportComment', this.comment);
        },
        showContentModal(feed){
            this.contentModalOpen = true;
            this.selectedFeed = feed;
        },
    }
};
</script>
