import {defineStore} from 'pinia';
import { sendRequest } from "../common.js";

export const useCommentModalStore = defineStore('commentModal', {
    state: () => ({
        model: null,
        comments: null,
        comment: null,
        isOpen: false,
        type: null,
    }),
    actions: {
        async openModal(model) {
            this.model = model;
            this.isOpen = true;
        },
        async closeModal() {
            this.model = null;
            this.isOpen = false;
        },
        async fetchComments() {
            let modelId = this.model.id;
            if(this.type === 'book'){
                modelId = this.model.pivot.book_id;
            }
            const response = await sendRequest( `/api/${this.type}/${modelId}/comments`);
            this.comments = response.data;
        },
        async storeComment(params) {
            let modelId = this.model.id;
            if(this.type === 'book'){
                modelId = this.model.pivot.book_id;
            }

            const response = await sendRequest('post', `/api/${this.type}/${modelId}/comments`, params);
            this.comment = response.data;
        }
    }
});
