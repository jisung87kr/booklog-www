<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user.js';
import BookcaseComponent from "../components/BookcaseComponent.vue";
import BookComponent from "../components/BookComponent.vue";
import LikeButton from "../components/buttons/LikeButton.vue";
import CommentButton from "../components/buttons/CommentButton.vue";
import ShareButton from "../components//buttons/ShareButton.vue";
import HeaderComponent from "../components/headerComponent.vue";
import {sendRequest} from "../common.js";

const userStore = useUserStore();
const loaded = ref(false);
const auth = ref(userStore.user);
const bookcase = ref(window.__bookcase);
const profileUser = ref(window.__profileUser);

const deleteBookcase = () => {
    if (confirm('정말 삭제하시겠습니까?')) {
        sendRequest('delete', `/api/users/${auth.value.username}/bookcases/${bookcase.value.id}`)
            .then(() => {
                location.href = `/@${auth.value.username}`;
            });
    }
};
onMounted(async () => {
    loaded.value = true;
});
</script>
<template>
    <transition name="slide-fade">
        <div v-if="loaded">
            <header-component>
                <div class="flex justify-between w-full">
                    <history-back-button></history-back-button>
                    <div class="font-bold">@{{profileUser.username}}님의 책장</div>
                    <div></div>
                </div>
            </header-component>
            <div class="container-fluid max-w-xl mx-auto w-full">
                <div class="bg-white rounded-xl">

                    <div class="p-6">
                        <div>{{ bookcase.title }}</div>
                        <div class="text-sm text-gray-600" v-if="bookcase.description">{{ bookcase.description }}</div>
                        <div class="grid grid-cols-2 gap-3" v-if="auth && auth.id == profileUser.id">
                            <a :href="'/@'+auth.username+'/bookcases/'+bookcase.id+'/edit'" class="rounded-xl block text-center px-3 py-2 border border-zinc-300 text-sm font-bold w-full my-6">책장 수정</a>
                            <button type="button" class="rounded-xl block text-center px-3 py-2 border border-red-300 text-sm font-bold w-full my-6 text-red-600" @click="deleteBookcase">삭제</button>
                        </div>
                        <div class="mt-2 flex gap-3">
                            <like-button :model="bookcase" :auth="auth" :type="'bookcase'"></like-button>
                            <comment-button :model="bookcase" :type="'bookcase'"></comment-button>
                            <share-button :model="bookcase" :type="'bookcase'"></share-button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-10">
                    <template v-for="book in bookcase.books" :key="book.id">
                        <book-component :book="book">
                            <template #footer>
                                <div class="flex gap-2 mt-2">
                                    <like-button :auth="auth" :model="book" :type="'book'"></like-button>
                                    <comment-button :auth="auth" :model="book" :type="'book'"></comment-button>
                                    <share-button :auth="auth" :model="book" :type="'book'"></share-button>
                                </div>
                            </template>
                        </book-component>
                    </template>
                </div>
            </div>
        </div>
    </transition>
</template>
