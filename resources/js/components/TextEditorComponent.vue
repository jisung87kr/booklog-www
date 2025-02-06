<script setup>
import Quill from 'quill';
import {nextTick, onMounted, ref, watch, defineProps, defineEmits, onUpdated} from "vue";
import { sendRequest } from "../common.js";
import {useCommentModalStore} from "../stores/commentStore.js";
import {usePostFormStore} from "../stores/postForm.js";
import debounce from 'lodash/debounce';

const postFormStore = usePostFormStore();
const commentModalStore = useCommentModalStore();
commentModalStore.$onAction(
    async ({
               name, // action 이름.
               store, // Store 인스턴스, `someStore`와 같음.
               args, // action으로 전달된 매개변수로 이루어진 배열.
               after, // action에서 return 또는 resolve 이후의 훅.
               onError, // action에서 throw 또는 reject 될 경우의 훅.
           }) => {
        if(name === 'storeComment'){
            after(async () => {
                clearEditor();
            });
        }
    }
)

postFormStore.$onAction(
    async ({
               name, // action 이름.
               store, // Store 인스턴스, `someStore`와 같음.
               args, // action으로 전달된 매개변수로 이루어진 배열.
               after, // action에서 return 또는 resolve 이후의 훅.
               onError, // action에서 throw 또는 reject 될 경우의 훅.
           }) => {
        if (name === 'updateContent') {
            console.log(args);
            content.value = args[0];
        }
    });

const props = defineProps({
    content: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '내용을 입력해주세요',
    }
});

const emit = defineEmits(['updateContent']);

const content = ref(props.content);
const quill = ref(null);
const editor = ref(null);

const fetchTags = debounce(async (searchTerm) => {
    const response = await sendRequest('GET', `/api/tags?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.name;
        return tag;
    });

    return result;
}, 300);

const fetchBooks = debounce(async (searchTerm) => {
    const response = await sendRequest('GET', `/api/books?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.title;
        return tag;
    });

    return result;
}, 300);

const fetchMentions = debounce(async (searchTerm) => {
    const response = await sendRequest('GET', `/api/users?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.username;
        return tag;
    });

    return result;
}, 300);
const initEditor = () => {
    quill.value = new Quill(editor.value, {
        placeholder: props.placeholder,
        toolbar: {
            container: [],
        },
        modules: {
            mention: {
                //allowedChars: /^[A-Za-z가-힣0-9\sÅÄÖåäö\-.\(\)]*$/,
                allowedChars: /^[\w가-힣\s\-\.\d]+$/,
                mentionDenotationChars: ["@", "#", "$"],
                source: async (searchTerm, renderList, mentionChar) => {
                    let values;

                    if (mentionChar === "@") {
                        values = await fetchMentions(searchTerm);
                    } else if(mentionChar === '$') {
                        values = await fetchBooks(searchTerm);
                    } else{
                        values = await fetchTags(searchTerm);
                    }

                    if (searchTerm.length === 0) {
                        renderList(values, searchTerm);
                    } else {
                        const matches = [];
                        for (let i = 0; i < values.length; i++)
                            if (
                                ~values[i].value.toLowerCase().indexOf(searchTerm.toLowerCase())
                            )
                                matches.push(values[i]);
                        renderList(matches, searchTerm);
                    }
                }
            }
        }
    });

    quill.value.on("text-change", () => {
        content.value = quill.value.root.innerHTML;
    });

    quill.value.root.innerHTML = content.value;

    nextTick(() => {
        quill.value.focus();
    });
};

const clearEditor = () => {
    quill.value.root.innerHTML = '';
};

onMounted(() => {
    nextTick(() => {
        initEditor();
    });
});

watch(content, (newContent) => {
    if (quill.value && newContent !== quill.value.root.innerHTML) {
        quill.value.root.innerHTML = newContent;
    }
    emit('updateContent', newContent, quill);
});

</script>
<template>
    <div id="editor" class="!outline-none !h-auto p-0 !mt-3 text-base" ref="editor"></div>
</template>
<style>
.ql-editor{
    padding: 0;
    height: auto !important;
    overflow: scroll;
    max-height: 50vh;
}

.ql-editor:focus-visible{
    outline: none !important;
}

.ql-editor.ql-blank::before{
    left: 0 !important;
}

.ql-mention-list-container{
    background: white;
    border: 1px solid #ccc;
    z-index: 10;
    border-radius: 10px;
    max-height: 200px;
    overflow: auto;
}

.ql-mention-list-item{
    padding: 10px;
    border-bottom: 1px solid #ccc;
    cursor: pointer;
}

.ql-mention-list-item.selected{
    background: #f4f4f4;
}
</style>
