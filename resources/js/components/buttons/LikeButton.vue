<template>
    <button type="button" @click="toggleLike(feed)">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="icon icon-tabler icon-tabler-heart"
             width="20"
             height="20"
             viewBox="0 0 24 24" stroke-width="1.5" :stroke="feed.like_id ? 'red' : '#000000'"
             :fill="feed.like_id ? 'red' : 'none' " stroke-linecap="round"
             stroke-linejoin="round"
        >
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"/>
        </svg>
    </button>
</template>
<script>
import {sendRequest} from "../../common.js";

export default {
    name: 'LikeButton',
    props: {
        feed: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: true,
        }
    },
    methods: {
        async toggleLike(feed){
            if(!this.auth){
                alert('로그인 후 이용해주세요');
                return false;
            }

            if(feed.like_id){
                await sendRequest('delete', `/api/users/${this.auth.id}/actions/${feed.like_id}`);
                feed.like_id = null;
            } else {
                let data = {
                    'action': 'reading_process_like',
                    'user_actionable_id': feed.id,
                    'user_actionable_type': 'processes',
                }
                let result = await sendRequest('post', `/api/users/${this.auth.id}/actions`, data);
                feed.like_id = result.data.id;
            }
        },
    }
}
</script>
