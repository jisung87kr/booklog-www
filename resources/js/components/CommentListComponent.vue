<template>
    <template v-if="comments.data.length > 0"
              v-for="comment in comments.data"
              :key="comment.id"
    >
        <comment-component :model="model"
                           :auth="auth"
                           :comment="comment"
                           class="py-3"
                           @remove-comment="removeComment(comment)"
                           @report-comment="reportComment(comment)"
        ></comment-component>
    </template>
    <div class="text-gray-800" v-else>등록된 댓글이 없습니다.</div>
    <div class="text-center mt-1 border-t pt-3"
         v-if="comments.total > 1 && comments.current_page < comments.last_page"
    >
        <button type="button"
                @click="moreComments"
                class="text-sm"
        >더보기 + </button>
    </div>
</template>
<script>
import CommentComponent from "./CommentComponent.vue";
import {sendRequest} from "../common.js";
export default{
    name: 'CommentListComponent',
    components: {
        CommentComponent,
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: false,
        }
    },
    mounted(){
        this.fetchComments(1);
    },
    data(){
        return{
            comments: {
                current_page: 1,
                data: [],
                last_page: null,
                total: null,
            },
        }
    },
    methods: {
        async fetchComments(page) {
            let params = {
                page: page,
            };
            const response = await sendRequest("GET", `api/posts/${this.model.id}/comments`, params);
            this.comments.current_page = response.data.current_page;
            this.comments.last_page = response.data.last_page;
            this.comments.total = response.data.total;
            this.comments.data = [...this.comments.data, ...response.data.data];

            this.$emit('fetchComments', this.model);
        },
        moreComments() {
            let page = this.comments.current_page + 1;
            this.fetchComments(page);
        },
        removeComment(comment) {
            this.comments.data = this.comments.data.filter((c) => c.id !== comment.id);
        },
        reportComment(comment) {
            this.$emit('reportComment', comment);
        },
    }
}
</script>
