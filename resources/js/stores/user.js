import { sendRequest } from "../common.js";
import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => ({
        isLogin: false,
        user: window.auth,
    }),
    actions: {
        async checkUser() {
            try {
                const response = await axios.get( '/api/user');
                if (response.data.data) {
                    this.user = response.data.data;
                    this.isLogin = true;
                } else {
                    this.user = null;
                    this.isLogin = false;
                }
            } catch (error) {
                this.user = null;
                this.isLogin = false;
            } finally {
                return this.user;
            }
        },
    },
});
