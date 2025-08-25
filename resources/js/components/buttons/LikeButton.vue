<template>
    <div class="flex items-center">
        <button type="button" @click="toggleLike(model)">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="icon icon-tabler icon-tabler-heart"
                 width="20"
                 height="20"
                 viewBox="0 0 24 24" stroke-width="1.5" :stroke="model.like_id ? '#ff2157' : '#000000'"
                 :fill="model.like_id ? '#ff2157' : 'none' " stroke-linecap="round"
                 stroke-linejoin="round"
            >
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"/>
            </svg>
        </button>
        <span class="ms-1 text-xs">{{model.like_count}}</span>
    </div>
</template>
<script>
import {sendRequest} from "../../common.js";

export default {
    name: 'LikeButton',
    props: {
        model: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: false,
        },
        type: {
            type: String,
            required: true,
        }
    },
    methods: {
        async toggleLike(model){
            if(!this.auth){
                alert('로그인 후 이용해주세요');
                return false;
            }

            if(model.like_id){
                await sendRequest('delete', `/api/users/${this.auth.username}/actions/${model.like_id}`);
                model.like_id = null;
                model.like_count--;
            } else {
                let modelId = model.id;
                if(this.type === 'book'){
                   modelId = model.pivot.book_id;
                }

                let data = {
                    'action': 'like',
                    'user_actionable_id': modelId,
                    'user_actionable_type': this.type,
                }
                let result = await sendRequest('post', `/api/users/${this.auth.username}/actions`, data);
                model.like_id = result.data.id;
                model.like_count++;
            }
        },
    }
}
</script>
