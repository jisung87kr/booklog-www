import {defineStore} from 'pinia';
export const usePostFormStore = defineStore('postForm', {
    state: () => ({
        post: null,
    }),
    actions: {
        async getPost(id) {
            try {
                const response = await axios.get( `/api/posts/${id}`);
                this.post = response.data.data;
            } catch (error) {
                this.post = null;
            } finally {
                return this.post;
            }
        },
        async createPost(data) {
            try {
                const response = await axios.post( '/api/posts', data);
                this.post = response.data.data;
            } catch (error) {
                this.post = null;
            } finally {
                return this.post;
            }
        },
        async updatePost(id, data) {
            try {
                const response = await axios.put( `/api/posts/${id}`, data);
                this.post = response.data.data;
            } catch (error) {
                this.post = null;
            } finally {
                return this.post;
            }
        },
        async deletePost(id) {
            try {
                const response = await axios.delete( `/api/posts/${id}`);
                this.post = response.data.data;
            } catch (error) {
                this.post = null;
            } finally {
                return this.post;
            }
        },
        updateContent(content) {
            console.log("@@@@@@@@@@@@@");
            console.log(content);
        }
    },
});
