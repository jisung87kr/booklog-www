<script setup>
import { ref, watch, onMounted, nextTick, defineEmits } from 'vue';
import ModalComponent from "./ModalComponent.vue";
import { sendRequest, getHashTags, getBooksTags, getMentions } from "../common.js";
import Quill from 'quill';
import {useUserStore} from "../stores/user.js";
import {usePostFormStore} from "../stores/postForm.js";
import AttachedImageComponent from "../components/AttachedImageComponent.vue";

const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const postFormStore = usePostFormStore();

const props = defineProps({
    auth: {
        type: Object,
        required: true
    },
    open: {
        type: Boolean,
        default: false
    }
});

const showModal = ref(props.open);
const content = ref(null);
const quill = ref(null);
const emit = defineEmits(['storePost']);
const fileInput = ref(null);
const images = ref([]);
const selectedFiles = ref([]); // 파일들을 추적하기 위한 변수

const openModal = () => {
    if(!auth.value){
        alert('로그인후 이용해주세요');
        window.location.href='/login';
        return false;
    }
    showModal.value = true;
    nextTick(() => {
        initEditor();
    });
};

const closeModal = () => {
    showModal.value = false;
};

const storePost = async () => {
    if(content.value === '' || !content.value){
       alert('내용을 입력해주세요');
       return false;
    }
    let params = {
        content: content.value,
        tags: getHashTags(content.value),
        bookTags: getBooksTags(content.value),
        mentions: getMentions(content.value),
    };

    //const response = await sendRequest('POST', '/api/posts', params);
    await postFormStore.createPost(params);
    //console.log(postFormStore.post.id);

    // 파일업로드
    await uploadImages('post', postFormStore.post.id, selectedFiles.value);

    showModal.value = false;
    content.value = '';
    emit('storePost', params);
};

const fetchTags = async (searchTerm) => {
    const response = await sendRequest('GET', `/api/tags?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.name;
        return tag;
    });

    return result;
};

const fetchBooks = async (searchTerm) => {
    const response = await sendRequest('GET', `/api/books?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.title;
        return tag;
    });

    return result;
};

const fetchMentions = async (searchTerm) => {
    const response = await sendRequest('GET', `/api/users?q=${searchTerm}`);

    const result = response.data.data.map(tag => {
        tag.value = tag.username;
        return tag;
    });

    return result;
};

const initEditor = () => {
    quill.value = new Quill(document.querySelector("#editor"), {
        placeholder: "새로운 감상이 있나요 ?",
        toolbar: {
            container: [],
        },
        modules: {
            mention: {
                allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
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
};

// 버튼 클릭 시 파일 input을 트리거하는 함수
const triggerFileInput = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const handleImages = (event) => {
    const files = event.target.files;

    Array.from(files).forEach((file) => {
        if (file && file.type.startsWith('image/')) {
            selectedFiles.value.push(file);
            const reader = new FileReader();
            reader.onload = (e) => {
                images.value.push(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
}

const deleteImage = (idx) => {
    selectedFiles.value.splice(idx, 1);
}

const uploadImages = async (type, id, files) => {
    const formData = new FormData();
    files.forEach((file) => {
        formData.append('images[]', file);
    });

    try {
        const response = await axios.post(`/api/${type}/${id}/images`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        console.log('Upload success:', response.data);
    } catch (error) {
        console.error('Upload failed:', error);
    }
};

onMounted(() => {
    if (props.open) {
        initEditor();
    }
});

watch(() => props.open, (newVal) => {
    showModal.value = newVal;
    if (newVal) {
        nextTick(() => {
            initEditor();
        });
    }
});

watch(content, (newContent) => {
    if (quill.value && newContent !== quill.value.root.innerHTML) {
        quill.value.root.innerHTML = newContent;
    }
});
</script>
<template>
    <button type="button" @click="openModal">
        <slot></slot>
    </button>
    <modal-component :is-visible="showModal"
                     @close="closeModal()">
        <template v-slot:modal-header>
            <div class="px-4 py-4 relative">
                <button type="button" class="absolute left-6 top-1/2 -translate-y-1/2 text-sm">취소</button>
                <div class="text-center text-bold">새로운 포스팅</div>
            </div>
        </template>
        <div class="border-t border-b p-4 pt-3 relative">
            <avatar-component :user="auth" :follow-button="false" :user-name="false"></avatar-component>
            <div id="editor" class="!outline-none !h-auto p-0 !mt-3" ref="editor"></div>
            <div id="previewContainer">
                <attached-image-component :images="images" @delete-image="deleteImage(idx)"></attached-image-component>
            </div>
            <div class="flex gap-3 mt-3">
                <button type="button" @click="triggerFileInput" class="flex items-center align-items-center">
                    <input type="file" multiple @change="handleImages" ref="fileInput" class="hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M15 8h.01"></path>
                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                    </svg>
                    <span class="text-sm ms-1" v-if="images.length > 0">추가</span>
                </button>
                <button type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M5 7h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2"></path>
                        <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                    </svg>
                </button>
<!--                <button type="button">-->
<!--                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">-->
<!--                        <path d="M5 9l14 0"></path>-->
<!--                        <path d="M5 15l14 0"></path>-->
<!--                        <path d="M11 4l-4 16"></path>-->
<!--                        <path d="M17 4l-4 16"></path>-->
<!--                    </svg>-->
<!--                </button>-->
                <button type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12z"></path>
                        <path d="M19 16h-12a2 2 0 0 0 -2 2"></path>
                        <path d="M9 8h6"></path>
                    </svg>
                </button>
            </div>
        </div>
        <template v-slot:modal-footer>
            <div class="flex px-4 py-4 border-b justify-end">
                <button type="button" class="px-4 py-1.5 text-sm border rounded-lg" @click="storePost()">게시</button>
            </div>
        </template>
    </modal-component>
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
