<template>
    <div class="flex items-center">
        <button type="button" @click="openModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2"
                 width="20"
                 height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1"/>
            </svg>
        </button>
        <span class="text-xs ms-1">{{model.comment_count}}</span>
    </div>
</template>
<script>
import {useCommentModalStore} from "../../stores/commentStore.js";
export default {
    name: 'CommentButton',
    props: {
        model: {
            type: Object,
            required: true
        },
        type: {
            type: String,
            required: true
        }
    },
    mounted(){
        this.commentModalStore = useCommentModalStore();
    },
    methods:{
        openModal(){
            this.commentModalStore.type = this.type;
            this.commentModalStore.openModal(this.model);
        }
    }
}
</script>
