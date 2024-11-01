<template>
    <form action="" class="mt-2" @submit.prevent="storeComment">
        <div class="flex gap-2">
            <input type="text"
                   placeholder="댓글 달기"
                   class="w-full border rounded-lg border-gray-400"
                   v-model="comment"
            >
            <button type="submit" class="shrink-0 text-xs">게시</button>
        </div>
    </form>
</template>
<script>
import {sendRequest} from "../common";
export default {
    name: 'CommentForm',
    data() {
        return {
            comment: '',
        }
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
        comments: {
            type: Object,
            required: false,
        },
        auth: {
            type: Object,
            required: false,
        },
    },
    methods: {
        async storeComment() {
            if(!this.auth){
                alert('로그인 후 이용해주세요');
                return false;
            }

            let params = {
                body: this.comment,
            };

            const response = await sendRequest('post', `/api/posts/${this.model.id}/comments`, params);
            const storedComment = response.data;
            this.comment = '';
            this.$emit('storedComment', storedComment);
        }
    }
}
</script>
