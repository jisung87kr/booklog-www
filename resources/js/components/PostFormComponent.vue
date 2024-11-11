<template>
    <button type="button" @click="openModal">쓰기</button>
    <modal-component :is-visible="showModal"
                     @close="closeModal()">
        <template v-slot:modal-header>
            <div class="px-4 py-4 relative">
                <button type="button" class="absolute left-6 top-1/2 -translate-y-1/2 text-sm">취소</button>
                <div class="text-center text-bold">새로운 포스팅</div>
            </div>
        </template>
        <div class="border-t border-b p-4 relative">
            <avatar-component :user="auth" :follow-button="false" :user-name="false"></avatar-component>
            <div id="editor" class="" ref="editor"></div>
            <div class="flex gap-3">
                <button type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M15 8h.01"></path>
                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
                    </svg>
                </button>
                <button type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M5 7h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2"></path>
                        <path d="M9 13a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                    </svg>
                </button>
                <button type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="17" height="17" stroke-width="2">
                        <path d="M5 9l14 0"></path>
                        <path d="M5 15l14 0"></path>
                        <path d="M11 4l-4 16"></path>
                        <path d="M17 4l-4 16"></path>
                    </svg>
                </button>
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
                <button type="button" class="px-4 py-1.5 text-sm border rounded-lg">게시</button>
            </div>
        </template>
    </modal-component>
</template>
<script>
import ModalComponent from "./ModalComponent.vue";
export default {
    components: {
        ModalComponent
    },
    props: {
        auth: {
            type: Object,
            required: true
        },
        open: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            auth: {
                username: 'username',
                name: 'name',
                profile_photo_url: 'https://via.placeholder.com/150'
            },
            showModal: false,
        }
    },
    mounted() {
        this.showModal = this.open;
    },
    methods:{
        openModal(){
          this.showModal = true;
            this.$nextTick(() => {
                this.initEditor();
            });
        },
        closeModal(){
            this.showModal = false;
        },
        getHashTags(){
            return this.$refs.editor.innerText.match(/#[a-zA-Z0-9ㄱ-ㅎ가-힣]+/g);
        },
        getMentions(){
            return this.$refs.editor.innerText.match(/@[a-zA-Z0-9ㄱ-ㅎ가-힣]+/g);
        },
        fetchTags(){
            const hashValues = [
                { id: 3, value: "Fredrik Sundqvist 2" },
                { id: 4, value: "Patrik Sjölin 2" }
            ];

            return hashValues;
        },
        fetchMentions(){
            const atValues = [
                { id: 1, value: "Fredrik Sundqvist" },
                { id: 2, value: "Patrik Sjölin" }
            ];

            return atValues;
        },
        initEditor(){
            const quill = new Quill(this.$refs.editor, {
                modules: {
                    mention: {
                        allowedChars: /^[A-Za-z\sÅÄÖåäö]*$/,
                        mentionDenotationChars: ["@", "#"],
                        source: (searchTerm, renderList, mentionChar) => {
                            let values;

                            if (mentionChar === "@") {
                                values = this.fetchMentions();
                            } else {
                                values = this.fetchTags();
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
        }
    }
}
</script>
