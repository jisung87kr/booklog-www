<template>
    <button type="button" :class="className" @click="toggleFollow"
            v-if="isFollowing != true">
        팔로잉
    </button>
    <button type="button" :class="activeClassName" @click="toggleFollow"
            v-else>
        팔로우
    </button>
</template>
<script>
import { sendRequest } from "../../common.js";
export default {
    name: 'FollowToggleButton',
    props: {
        user: {
            type: Object,
            required: true
        },
        className:{
            type: String,
            default: 'rounded-lg px-3 py-2 text-sm font-bold border border-gray-300'
        },
        activeClassName:{
            type: String,
            default: 'rounded-lg px-3 py-2 bg-zinc-800 text-white text-sm font-bold'
        }
    },
    mounted(){
    },
    data(){
        return {
            isFollowing: this.user.is_following,
        }
    },
    methods: {
        toggleFollow() {
            if (this.isFollowing) {
                this._unFollow();
            } else {
                this._follow();
            }
        },
        async _follow() {
            const response = await sendRequest('post', '/api/follows', { user_id: this.user.id });
            this.isFollowing = true;
        },
        async _unFollow() {
            const response = await sendRequest('delete', '/api/follows/' + this.user.username);
            this.isFollowing = false;
        }
    }
}
</script>
