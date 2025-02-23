<template>
    <button type="button" :class="activeClassName" @click="toggleFollow"
            v-if="isFollowing != true">
        팔로우
    </button>
    <button type="button"
            :class="[className, {'border-red-700 text-red-700': hover}]"
            @click="toggleFollow"
            @mouseenter="hover = true"
            @mouseleave="hover = false"
            v-else>
        <span v-if="hover">언팔로우</span>
        <span v-else>팔로우 중</span>
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
            hover: false,
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
            try {
                const response = await axios.post( '/api/follows', { user_id: this.user.id });
                this.isFollowing = true;
            } catch (e) {
                if(e.response.status == 401){
                    alert('로그인이 필요합니다.');
                    location.href = '/login';
                }
            }
        },
        async _unFollow() {
            const response = await sendRequest('delete', '/api/follows/' + this.user.username);
            this.isFollowing = false;
        }
    }
}
</script>
