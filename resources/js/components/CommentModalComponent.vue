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
        <div class="p-3">
            <div class="mb-3 font-bold">댓글</div>
        </div>
    </template>
    <div class="p-3">
        <div>
            <comment-list-component :model="commentModalStore.model"></comment-list-component>
        </div>
    </div>
    <template v-slot:modal-footer>
        <div class="p-3 border-t">
            <div class="flex gap-2">
                <like-button :auth="auth" :model="commentModalStore.model"></like-button>
                <share-button :feed="commentModalStore.model"></share-button>
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
