<template>
    <div class="flex items-center">
        <button type="button" @click="handelClick(model)">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="20"
                 height="20"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round"
                 stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 14l11 -11"/>
                <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"/>
            </svg>
        </button>
        <span class="ms-1 text-xs">{{model.share_count}}</span>
    </div>
</template>
<script>
import {sendRequest} from "../../common.js";
import {useUserStore} from "../../stores/user.js";

export default{
    name: 'ShareButton',
    props: {
        model: {
            type: Object,
            required: true
        },
        type: {
            type: String,
            required: true
        }
    },
    data(){
        return {
            auth: null,
        }
    },
    mounted() {
        const userStore = useUserStore();
        this.auth = userStore.user;
    },
    methods: {
        async handelClick(model){
            let modelId = model.id;
            if(this.type === 'book'){
                modelId = model.pivot.book_id;
            }

            this.$emit('click');
            let data = {
                'action': 'share',
                'user_actionable_id': modelId,
                'user_actionable_type': this.type,
            }
            let result = await sendRequest('post', `/api/actions`, data);
            this.copyUrl(model);
            model.share_count++;
        },
        copyUrl(model){
            let url = '';
            switch (this.type){
                case 'bookcase':
                    url = window.location.origin + '/bookcases/' + model.id;
                    break;
                case 'post':
                    url = window.location.origin + '/feeds/' + model.id;
                    break;
                case 'book':
                    //url = window.location.origin + '/books/' + model.book_id;
                    url = model.link;
                    break;
            }

            const textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('URL이 복사되었습니다.');
        }
    }
}
</script>
