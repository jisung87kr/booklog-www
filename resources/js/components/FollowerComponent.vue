<template>
    <div>
        <div class="w-full">
            <div class="flex justify-between">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center">
                        <div class="profile shrink-0 mr-3">
                            <img class="w-8 h-8 rounded-full" :src="follow.follower_user.profile_photo_url">
                        </div>
                        <div class="mr-3 break-keep">
                            <span class="font-bold">{{ follow.follower_user.name }}</span>
                            <span>님이 회원님을 팔로우하기 시작했습니다.</span>
                            <span class="text-sm text-gray-500 ms-1">{{ follow.created_at_human }}</span>
                        </div>
                    </div>
                    <div class="shrink-0 ms-3">
                        <button type="button"
                                class="bg-blue-500 text-white rounded-lg px-4 py-2 font-bold text-sm"
                                v-if="!follow.is_following"
                                @click="_follow(follow.follower_user.id)"
                        >팔로우</button>
                        <button type="button"
                                class="bg-gray-500 text-white rounded-lg px-4 py-2 font-bold text-sm"
                                v-else
                                @click="_unFollow(follow.follower_user.id)"
                        >팔로잉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { sendRequest } from "../common";

export default {
    name: 'FollowerComponent',
    props: {
        follow: {
            type: Object,
            required: false
        },
        auth: {
            type: Object,
            required: false,
        },
    },
    methods: {
        async _follow(userId) {
            const response = await sendRequest('post', '/api/follows', { user_id: userId });
            this.follow.is_following = true;
        },
        async _unFollow(userId) {
            const response = await sendRequest('delete', '/api/follows/' + userId);
            this.follow.is_following = false;
        }
    }
};
</script>
