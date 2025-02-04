<script setup>
import {ref, defineProps, defineEmits, onMounted} from 'vue';
import {sendRequest} from "../common.js";
import {useUserStore} from "../stores/user.js";
import {useCommentModalStore} from "../stores/commentStore.js";
const userStore = useUserStore();
const commentModalStore = useCommentModalStore();
const auth = ref(userStore.user);

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
        } else if(name === 'storeComment'){
            after(async () => {
                comments.value = {
                    current_page: 1,
                    data: [],
                    last_page: null,
                    total: null,
                };

                fetchComments(comments.value.current_page);
            });
        }
    }
)

const props = defineProps({
   model: {
       type: Object,
       required: true,
   },
});

const emit = defineEmits(['fetchComments', 'reportComment']);

const comments = ref({
    current_page: 1,
    data: [],
    last_page: null,
    total: null,
});

const fetchComments = async(page) => {
    let params = {
        page: page,
    };

    let modelId = props.model.id;
    if(commentModalStore.type === 'book'){
        modelId = props.model.pivot.book_id;
    }

    const response = await sendRequest("GET", `/api/${commentModalStore.type}/${modelId}/comments`, params);
    comments.value.current_page = response.data.current_page;
    comments.value.last_page = response.data.last_page;
    comments.value.total = response.data.total;
    comments.value.data = [...comments.value.data, ...response.data.data];

    // 중복제거
    comments.value.data = comments.value.data.filter((comment, index, self) => self.findIndex(t => t.id === comment.id) === index);

    emit('fetchComments', props.model);
};
const moreComments = () => {
    let page = comments.value.current_page + 1;
    fetchComments(page);
};

const removeComment = (comment) => {
    comments.value.data = comments.value.data.filter((c) => c.id !== comment.id);
}
const reportComment = (comment) => {
    emit('reportComment', comment.value);
}

onMounted(async () => {
    await fetchComments(1);
});

</script>
<template>
    <div v-if="comments.data.length > 0">
        <template v-for="comment in comments.data"
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
    </div>
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
