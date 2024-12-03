<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, defineProps } from 'vue';
import {useUserStore} from "../stores/user.js";
import {useCommentModalStore} from "../stores/commentStore.js";
import CommentListComponent from "./CommentListComponent.vue";
const userStore = useUserStore();
const auth = ref(userStore.user);
const commentModalStore = useCommentModalStore();

const scrollBottom = () => {
    nextTick(() => {
        const modalContent = document.querySelector(".modal-body");
        modalContent.scrollTo({
            top: modalContent.scrollHeight,
            behavior: "smooth",
        });
    });
};

</script>
<template>
    <modal-component :is-visible="commentModalStore.isOpen"
                     @close="commentModalStore.closeModal"
    >
    <template v-slot:modal-header>
        <div class="px-6 py-3">
            <div class="mb-3 font-bold">댓글</div>
        </div>
    </template>
    <div class="px-6 py-3">
        <div>
            <comment-list-component :model="commentModalStore.model" :type="commentModalStore.type"></comment-list-component>
        </div>
    </div>
    <template v-slot:modal-footer>
        <div class="px-6 py-3 border-t">
            <div class="flex gap-2">
                <like-button :auth="auth" :model="commentModalStore.model" :type="commentModalStore.type"></like-button>
                <share-button :model="commentModalStore.model" :type="commentModalStore.type"></share-button>
            </div>
            <div class="mt-3" v-if="auth">
                <comment-form :model="commentModalStore.model"
                              @stored-comment="scrollBottom"
                ></comment-form>
            </div>
        </div>
    </template>
    </modal-component>
</template>
