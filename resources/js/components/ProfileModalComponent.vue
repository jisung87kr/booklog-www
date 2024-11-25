<script setup>
import {sendRequest} from "../common.js";
import { ref, onMounted, defineProps, defineEmits } from 'vue';
import {useUserStore} from "../stores/user.js";
const userStore = useUserStore();
const auth = ref(userStore.user);
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const user = ref(props.user);
console.log(user.value);

const showModal = ref(false);

const update = async () => {
    const response = await sendRequest('PUT', '/api/user', user.value);
    console.log(user, response.data);
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
                        <button type="button" class="rounded-full w-[60px] h-[60px] overflow-hidden border">
                            <img :src="user.profile_photo_url" alt="">
                        </button>
                    </div>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">이름</div>
                    <div>{{user.name}}</div>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">소개</div>
                    <div v-if="user.introduction">
                        {{user.introduction}}
                    </div>
                    <button type="button" v-else>+ 소개 작성</button>
                </div>
                <div class="py-3 border-b">
                    <div class="font-bold">링크</div>
                    <a :href="user.link" target="_blank" v-if="user.link" class="text-blue-600">{{user.link}}</a>
                    <button type="button" v-else>+ 링크 추가</button>
                </div>
                <div class="py-3 flex gap-9 items-center">
                    <div>
                        <div class="font-bold">비공개 프로필</div>
                        <div class="text-sm text-gray-600">비공개 프로필로 전환하면 상대방이 회원님을 팔로우하지 않는 한 다른 사람에게 답글을 남길 수 없게 됩니다.</div>
                    </div>
                    <div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer">
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
