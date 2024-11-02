<template>
    <div class="flex justify-between align-items-center p-6">
        <div class="flex">
            <div class="shrink-0 mr-3">
                <img :src="user.profile_photo_url" alt="" class="w-8 h-8 rounded-full bg-gray-100">
            </div>
            <div class="">
                <div>{{user.username}}</div>
                <div class="opacity-50">
                    <a :href="'/@'+user.username">{{user.name}}</a>
                </div>
                <slot name="follower-count">
                    <div class="mt-3 text-sm">{{count}}</div>
                </slot>
            </div>
        </div>
        <slot name="followButton">
            <div>
                <follow-toggle-button :user="user"></follow-toggle-button>
            </div>
        </slot>
    </div>
</template>

<script>
import FollowToggleButton from "./buttons/FollowToggleButton.vue";
export default{
    name: 'AvatarComponent',
    components: {
        FollowToggleButton
    },
    props: {
        user: {
            type: Object,
            required: true
        },
    },
    mounted() {
        console.log(this.user);
    }
}
</script>
