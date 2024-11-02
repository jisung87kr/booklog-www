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
                            <span class="font-bold">
                                <a :href="'/@'+follow.follower_user.username">{{ follow.follower_user.username }}</a>
                            </span>
                            <span>님이 회원님을 팔로우하기 시작했습니다.</span>
                            <span class="text-sm text-gray-500 ms-1">{{ follow.created_at_human }}</span>
                        </div>
                    </div>
                    <div class="shrink-0 ms-3">
                        <follow-toggle-button :user="follow.follower_user"></follow-toggle-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FollowToggleButton from "./buttons/FollowToggleButton.vue";

export default {
    name: 'FollowerComponent',
    components: {
        FollowToggleButton
    },
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
};
</script>
