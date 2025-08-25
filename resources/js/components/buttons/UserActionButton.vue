<template>
    <button type="button" :class="className" @click="toggleAction(model)">
        <slot></slot>
    </button>
</template>
<script>
import {sendRequest, copyText} from "../../common.js";
export default{
    name: 'UserActionButton',
    data(){
        return {
        }
    },
    props: {
        auth: {
            type: Object,
            required: false,
        },
        model: {
            type: Object,
            required: true,
        },
        actionName: {
            type: String,
            required: true,
        },
        actionableType: {
            type: String,
            required: true,
        },
        className: {
            type: String,
            required: false,
        }
    },
    methods: {
        async toggleAction(model){
            if(!this.auth){
                alert('로그인 후 이용해주세요');
                return false;
            }

            switch (this.actionName) {
                case 'bookmark':
                    await this.toggleBookmark(model);
                    break;
                case 'like':
                    await this.toggleLike(model);
                    break;
                case 'uninterested':
                    await this.toggleUninterested(model);
                    break;
                case 'share':
                    await this.toggleShare(model);
                    break;
                case 'block':
                    await this.toggleBlock(model);
                    break;
                case 'report':
                    await this.toggleReport(model);
                    break;
                case 'show_profile':
                    await this.toggleShowProfile(model);
                    break;
                case 'follow':
                    await this.toggleFollow(model);
                    break;
                default:
                    console.log('action not found');
            }
        },
        async toggleBookmark(model){
            if(model.bookmark_id){
                await this.deleteAction(model.bookmark_id);
                model.bookmark_id = null;
            } else {
                let result = await this.storeAction('bookmark', model.id, this.actionableType);
                model.bookmark_id = result.id;
            }

            console.log(this.model.bookmark_id);
        },
        async toggleLike(model){
            if(model.like_id){
                await this.deleteAction(model.like_id);
                model.like_id = null;
            } else {
                let result = await this.storeAction('like', model.id, this.actionableType);
                model.like_id = result.id;
            }
        },
        async toggleUninterested(model){
            if(model.uninterested_id){
                await this.deleteAction(model.uninterested_id);
                model.uninterested_id = null;
            } else {
                let result = await this.storeAction('uninterested', model.id, this.actionableType);
                model.uninterested_id = result.id;
            }
        },
        async toggleShare(model){
            let result = await this.storeAction('share', model.id, this.actionableType);
            model.share_id = result.id;

            let url = '';
            switch (this.actionableType) {
                case 'post':
                    url = window.location.origin + '/feeds/' + model.id;
                    break;
                default:
                    console.log('actionableType not found');
            }
            copyText(url);
        },
        async toggleBlock(model){
            if(model.block_id){
                await this.deleteAction(model.block_id);
                model.block_id = null;
            } else {
                let result = await this.storeAction('block', model.id, this.actionableType);
                model.block_id = result.id;
            }
        },
        async toggleReport(model){
            if(model.report_id){
                await this.deleteAction(model.report_id);
                model.report_id = null;
            } else {
                let result = await this.storeAction('report', model.id, this.actionableType);
                model.report_id = result.id;
            }
        },
        async toggleShowProfile(model){
            if(model.show_profile_id){
                await this.deleteAction(model.show_profile_id);
                model.show_profile_id = null;
            } else {
                let result = await this.storeAction('show_profile', model.id, this.actionableType);
                model.show_profile_id = result.id;
            }
        },
        async storeAction(actionName, actionableId, actionableType){
            let data = {
                'action': actionName,
                'user_actionable_id': actionableId,
                'user_actionable_type': actionableType,
            }
            const response = await sendRequest('post', `/api/users/${this.auth.username}/actions`, data);
            this.$emit('actionStored', response.data);
            return response.data;
        },
        async deleteAction(actionId){
            const response = await sendRequest('delete', `/api/users/${this.auth.username}/actions/${actionId}`);
            this.$emit('actionDeleted', response.data);
            return response.data;
        }
    },
}
</script>
