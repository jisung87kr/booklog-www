<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, defineProps } from 'vue';
import {useUserStore} from "../stores/user.js";
import {useCommentModalStore} from "../stores/commentStore.js";
const userStore = useUserStore();
const auth = ref(userStore.user);
const commentModalStore = useCommentModalStore();

commentModalStore.$onAction(
    async ({
        name, // action 이름.
        store, // Store 인스턴스, `someStore`와 같음.
        args, // action으로 전달된 매개변수로 이루어진 배열.
        after, // action에서 return 또는 resolve 이후의 훅.
        onError, // action에서 throw 또는 reject 될 경우의 훅.
    }) => {
        if(name === 'openModal'){
            after(async () => {
            });
        } else if(name === 'closeModal'){
            after(async () => {
            });
        } else if(name === 'fetchComments'){
            after(async () => {
            });
        }
    }
)

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
            <comment-list :model="commentModalStore.model"
                          :auth="auth"
            ></comment-list>
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
                              :auth="auth"
                              @stored-comment="scrollBottom"
                ></comment-form>
            </div>
        </div>
    </template>
    </modal-component>
</template>
