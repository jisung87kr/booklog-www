<template>
    <div class="flex justify-between align-items-center p-6">
        <div class="flex">
            <div class="shrink-0 mr-3">
                <img :src="user.profile_photo_url" alt="" class="w-8 h-8 rounded-full bg-gray-100">
            </div>
            <div class="">
                <div>{{user.email}}</div>
                <div class="opacity-50">{{user.name}}</div>
                <slot name="follower-count">
                    <div class="mt-3 text-sm">{{count}}</div>
                </slot>
            </div>
        </div>
        <slot name="followButton">
            <div>
                <button type="button" class="border rounded-lg px-3 py-2 text-sm">팔로우</button>
            </div>
        </slot>
    </div>
</template>

<script>
export default{
    name: 'AvatarComponent',
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
