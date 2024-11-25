<script setup>
import {sendRequest, jsonToFormData} from "../common.js";
import { ref, onMounted, defineProps, defineEmits } from 'vue';
import {useUserStore} from "../stores/user.js";
import ModalComponent from "./ModalComponent.vue";
import DropdownComponent from "./DropdownComponent.vue";
const userStore = useUserStore();
const auth = ref(userStore.user);
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const user = ref(props.user);
const showModal = ref(false);
const showIntroductionEditModal = ref(false);
const showLinkEditModal = ref(false);
const fileRef = ref(null);
const userOld = {...user.value};
const photo = ref(null);

const update = async () => {
    const formData = jsonToFormData(user.value);
    formData.append('photo', photo.value);
    formData.append('_method', 'PUT');
    const response = await sendRequest('POST', '/api/user', formData);
    showModal.value = false;
};

const openIntroductionEditModal = () => {
    showIntroductionEditModal.value = true;
};

const openLinkEditModal = () => {
    showLinkEditModal.value = true;
};

const closeIntroductionEditModal = () => {
    user.value = {...userOld};
    showIntroductionEditModal.value = false;
};

const closeLinkEditModal = () => {
    user.value = {...userOld};
    showLinkEditModal.value = false;
};

const clickFileInput = () => {
    fileRef.value.click();
};

const handleFile = () => {
    const file = fileRef.value.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
        user.value.profile_photo_url = e.target.result;
        photo.value = file;
    };
    reader.readAsDataURL(file);
};
</script>
<template>
    <div>
        <slot>
            <button type="button" class="rounded-xl px-3 py-2 border border-zinc-300 text-sm font-bold w-full mt-6" @click="showModal = true">프로필 수정</button>
        </slot>
        <modal-component :is-visible="showModal"
                         @close="showModal = false">
            <div class="px-6 py-4">
                <div class="py-3 flex gap-9">
                    <div class="w-full border-b">
                        <div class="font-bold">사용자 이름</div>
                        <div>{{user.username}}</div>
                    </div>
                    <div>
                        <dropdown-component>
                            <template #mybutton>
                                <button type="button" class="rounded-full w-[60px] h-[60px] overflow-hidden border">
                                    <img :src="user.profile_photo_url" alt="">
                                </button>
                            </template>
                            <div class="absolute right-0 bottom-[-105px] shrink-0 bg-white rounded-lg border shadow py-3 w-[200px]">
                                <div class="px-6 py-1.5">
                                    <button type="button" class="" @click="clickFileInput">사진 업로드</button>
                                    <input type="file" class="hidden" ref="fileRef" @change="handleFile">
                                </div>
                                <div class="px-6 py-1.5">
                                    <button type="button" class="text-red-600">이미지 삭제</button>
                                </div>
                            </div>
                        </dropdown-component>
                    </div>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">이름</div>
                    <div>{{user.name}}</div>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">소개</div>
                    <button type="button" @click="openIntroductionEditModal">
                        <div v-if="user.introduction">
                            {{user.introduction}}
                        </div>
                        <button type="button" v-else>+ 소개 작성</button>
                    </button>
                    <modal-component :is-visible="showIntroductionEditModal" width="650px" @close="closeIntroductionEditModal">
                        <template #modal-header>
                            <div class="px-6 py-4 flex justify-between border-b">
                                <button type="button" class="font-bold" @click="closeIntroductionEditModal">취소</button>
                                <div class="font-bold text-xl">소개 수정</div>
                                <button type="button" class="font-bold" @click="showIntroductionEditModal = false">완료</button>
                            </div>
                        </template>
                        <div class="rounded-b-lg overflow-hidden">
                            <textarea class="w-full h-32 px-6 py-3 border-none" v-model="user.introduction"></textarea>
                        </div>
                    </modal-component>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">링크</div>
                    <div>
                        <a :href="user.link" target="_blank" v-if="user.link" class="text-blue-600">{{user.link}}</a>
                    </div>
                    <button type="button" @click="openLinkEditModal" class="text-sm">
                        <span v-if="user.link">+ 링크 수정</span>
                        <span v-else>+ 링크 추가</span>
                    </button>
                    <modal-component :is-visible="showLinkEditModal" width="650px" @close="closeLinkEditModal">
                        <template #modal-header>
                            <div class="px-6 py-4 flex justify-between border-b">
                                <button type="button" class="font-bold" @click="closeLinkEditModal">취소</button>
                                <div class="font-bold text-xl">링크 수정</div>
                                <button type="button" class="font-bold" @click="showLinkEditModal = false">완료</button>
                            </div>
                        </template>
                        <div class="rounded-b-lg overflow-hidden">
                            <textarea class="w-full h-32 px-6 py-3 border-none" v-model="user.link"></textarea>
                        </div>
                    </modal-component>
                </div>
                <div class="py-3 flex gap-9 items-center">
                    <div>
                        <div class="font-bold">비공개 프로필</div>
                        <div class="text-sm text-gray-600">비공개 프로필로 전환하면 상대방이 회원님을 팔로우하지 않는 한 다른 사람에게 답글을 남길 수 없게 됩니다.</div>
                    </div>
                    <div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" v-model="user.is_secret" :true-value="1" :false-value="0">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:w-5 after:h-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            <template #modal-footer>
                <div class="px-6 py-4">
                    <button type="button" class="px-3 py-3 bg-gray-800 text-white rounded-lg w-full text-center font-bold" @click="update">완료</button>
                </div>
            </template>
        </modal-component>
    </div>
</template>
<style scoped>
    textarea,
    textarea:focus,
    textarea:active,
    textarea:focus-visible{
        outline: none !important;
        border-color: transparent !important;
    }
</style>
