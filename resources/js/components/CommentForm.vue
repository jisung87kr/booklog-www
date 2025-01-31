<script setup>
import {ref, defineProps, defineEmits} from 'vue';
import TextEditorComponent from "./TextEditorComponent.vue";
import {useUserStore} from "../stores/user.js";
import {useCommentModalStore} from "../stores/commentStore.js";

const userStore = useUserStore();
const auth = ref(userStore.user);
const commentModalStore = useCommentModalStore();

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
    comments: {
        type: Object,
        required: false,
    },
});

const emit = defineEmits(['storedComment']);
const comment = ref('');
const editor = ref(null);
const editorRef = ref(null);

const storeComment = async () => {
    if(!auth.value){
        //alert('로그인 후 이용해주세요');
        return false;
    }

    let params = {
        body: comment.value,
    };

    await commentModalStore.storeComment(params);
    comment.value = '';
    emit('storedComment', commentModalStore.comment);
};
const changeContent = (newContent) => {
    comment.value = newContent;
};
</script>
<template>
    <form action="" class="mt-2" @submit.prevent="storeComment">
        <text-editor-component :content="comment" @update-content="changeContent" placeholder="새로운 감상이 있나요?" ref="editorRef"></text-editor-component>
        <div class="mt-1 text-right">
            <button type="submit" class="shrink-0 text-xs border rounded-lg px-3 py-2">게시</button>
        </div>
    </form>
</template>
