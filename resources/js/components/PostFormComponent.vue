<script setup>
import { ref, watch, onMounted, nextTick, defineEmits } from 'vue';
import ModalComponent from "./ModalComponent.vue";
import { sendRequest, getHashTags, getBooksTags, getMentions } from "../common.js";
import Quill from 'quill';
import {useUserStore} from "../stores/user.js";
import {usePostFormStore} from "../stores/postForm.js";
import AttachedImageComponent from "../components/AttachedImageComponent.vue";
import TextEditorComponent from "./TextEditorComponent.vue";
import debounce from "lodash/debounce.js";

const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const postFormStore = usePostFormStore();

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    model: {
        type: Object,
        default: null
    }
});

const showModal = ref(props.open);
const showSearchBookModal = ref(false);
const content = ref('');
const emit = defineEmits(['storePost']);
const fileInput = ref(null);
const images = ref([]);
const selectedFiles = ref([]); // 파일들을 추적하기 위한 변수
const q = ref(null);
const books = ref([]);

const openModal = () => {
    if(!auth.value){
        alert('로그인후 이용해주세요');
        window.location.href='/login';
        return false;
    }
    showModal.value = true;
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
        parent_id: props.model ? props.model.id : null,
        original_parent_id: props.model ? (props.model.original_parent_id ? props.model.original_parent_id : props.model.id) : null,
    };

    await postFormStore.createPost(params);

    // 파일업로드
    await uploadImages('post', postFormStore.post.id, selectedFiles.value);

    showModal.value = false;
    content.value = '';
    emit('storePost', params);
    window.location.href = '/home';
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

const changeContent = (newContent) => {
    content.value = newContent;
}

const openSearchBookModal = () => {
    showSearchBookModal.value = true;
}

const searchBooks = debounce(async () => {
    const response = await fetchBooks();
    books.value = response.data.data;
}, 300);

const fetchBooks = async () => {
    try {
        let params = {
            q: q.value,
        }
        return sendRequest('GET', '/api/service/books', params);
    } catch (error) {
        console.error(error);
    }
};

//TODO : 책 선택시 content에 추가
const selectBook = (book) => {
    content.value = `#${book.title}`;
    showSearchBookModal.value = false;
    postFormStore.updateContent(content.value);
}

onMounted(() => {

});

watch(() => props.open, (newVal) => {
    showModal.value = newVal;
});

</script>
<template>
    <div @click="openModal">
        <slot></slot>
    </div>
    <modal-component :is-visible="showModal"
                     @close="closeModal()">
        <template v-slot:modal-header>
            <div class="px-4 py-4 relative">
                <button type="button" class="absolute left-6 top-1/2 -translate-y-1/2 text-sm" @click="closeModal()">취소</button>
                <div class="text-center text-bold">새로운 포스팅</div>
            </div>
        </template>
        <div class="border-t border-b p-4 pt-3 relative">
            <avatar-component :user="auth" :follow-button="false" :user-name="false"></avatar-component>
            <text-editor-component :content="content"
                                   @update-content="changeContent"
                                   placeholder="새로운 감상이 있나요?"
            ></text-editor-component>
            <div id="previewContainer">
                <attached-image-component :images="images" @delete-image="deleteImage(idx)"></attached-image-component>
            </div>

            <!-- 인용일떄 노출 -->
            <template v-if="props.model">
                <div class="p-3 border rounded-lg mt-3 bg-gray-50">
                    <div v-html="props.model.content" class="text-xs"></div>
                </div>
            </template>

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
                <button type="button" @click="openSearchBookModal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12z"></path>
                        <path d="M19 16h-12a2 2 0 0 0 -2 2"></path>
                        <path d="M9 8h6"></path>
                    </svg>
                </button>
                <modal-component :is-visible="showSearchBookModal" width="80%">
                    <template v-slot:modal-header>
                        <div class="px-4 py-4 relative">
                            <button type="button" class="absolute left-6 top-1/2 -translate-y-1/2 text-sm" @click="closeModal()">취소</button>
                            <div class="text-center text-bold">도서 검색</div>
                        </div>
                    </template>
                    <div class="border-t border-b p-4 pt-3 relative">
                        <input type="text"
                               name="book_title"
                               class="w-full border border-gray-300"
                               placeholder="도서명을 검색하세요"
                               v-model="q"
                               @keyup="searchBooks"
                        >
                        <template v-if="books.length > 0">
                            <div class="mt-3 max-h-[60vh] overflow-y-auto">
                                <button v-for="book in books" :key="book.id"
                                        type="button"
                                        class="w-full flex py-2 border-b text-left"
                                        @click="selectBook(book)"
                                >
                                    <div class="shrink-0 mr-3 overflow-hidden border rounded-xl aspect-square w-[70px]">
                                        <img :src="book.cover_image" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="w-full">
                                        <div class="font-bold line-clamp-2">{{book.title}}</div>
                                        <div class="text-gray-600 text-xs line-clamp-1 mb-1">{{book.publisher}} / {{book.author}}</div>
                                        <div class="text-gray-600 text-xs line-clamp-1">{{book.contents}}</div>
                                    </div>
                                </button>
                                <div class="text-center mt-3">
                                    <button type="button" class="border rounded-xl px-3 py-2">더보기</button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="py-2 text-sm">검색된 내용이 없습니다.</div>
                        </template>
                    </div>
                </modal-component>
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
