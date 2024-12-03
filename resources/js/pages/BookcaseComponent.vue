<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user.js';
import BookcaseComponent from "../components/BookcaseComponent.vue";
import BookComponent from "../components/BookComponent.vue";
import LikeButton from "../components/buttons/LikeButton.vue";
import CommentButton from "../components/buttons/CommentButton.vue";
import ShareButton from "../components//buttons/ShareButton.vue";

const userStore = useUserStore();
const auth = ref(userStore.user);
const bookcase = ref(window.__bookcase);
console.log(window.__bookcase);

</script>
<template>
    <div class="container-fluid max-w-xl mx-auto w-full sm:pt-3">
        <div class="grid grid-cols-3 gap-3">
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
</template>
