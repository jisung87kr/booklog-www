import '../bootstrap.js';
import { createApp } from "vue";
import {sendRequest} from "../common.js";
import AvatarComponent from "../components/AvatarComponent.vue";
import FeedComponent from "../components/FeedComponent.vue";
import ModalComponent from "../components/ModalComponent.vue";
import CommentListComponent from "../components/CommentListComponent.vue";
import CommentForm from "../components/CommentForm.vue";
import LikeButton from "../components/buttons/LikeButton.vue";
import ShareButton from "../components/buttons/ShareButton.vue";
import FollowerComponent from "../components/FollowerComponent.vue";
import CommentComponent from "../components/CommentComponent.vue";
import followToggleButton from "../components/buttons/FollowToggleButton.vue";
import dropdownComponent from "../components/DropdownComponent.vue";
import UserActionButton from "../components/buttons/UserActionButton.vue";

createApp({
    components: {
        "avatar-component": AvatarComponent,
        "feed-component": FeedComponent,
        "modal-component": ModalComponent,
        "comment-list": CommentListComponent,
        "comment-component": CommentComponent,
        "comment-form": CommentForm,
        "like-button": LikeButton,
        "share-button": ShareButton,
        "follower-component": FollowerComponent,
        "follow-toggle-button": followToggleButton,
        "dropdown-component": dropdownComponent,
        "user-action-button": UserActionButton,
    },
    data() {
        return {
            selectedActivityType: 'post',
            activityTypes: [
                {key: 'post', value: '포스트'},
                {key: 'reply', value: '답글'},
                {key: 'quotation', value: '리포스트'},
            ],
            user: window.userData,
            auth: null,
            list: {
                current_page: 1,
                data: [],
                last_page: null,
                total: null,
            },
            loading: false,
            modalOpen: false,
            contentModalOpen: false,
            selectedFeed: {
                id: null,
                user: {
                    name: null,
                    profile_photo_url: null,
                },
                note: null,
                images: [],
            },
        };
    },
    async mounted(){
        window.addEventListener("scroll", this.handleScroll);
        this.auth = await this.fetchUser();
        await this.getList(1);
    },
    methods: {
        async fetchUser() {
            try {
                const response = await axios.get(`/api/user`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchPosts() {
            try {
                let params = {
                    user_id: this.user.id,
                }
                const response = await axios.get(`/api/posts`, {
                    params: params,
                });
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchReplies() {
            try {
                const response = await axios.get(`/api/users/${this.user.username}/activity/replies`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchQuotation() {
            try {
                const response = await axios.get(`/api/users/${this.user.username}/activity/quotations`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        showContentModal(feed){
            this.contentModalOpen = true;
            this.selectedFeed = feed;
        },
        async handleScroll() {
            // 현재 스크롤 위치 계산
            const scrollTop = window.scrollY; // 스크롤 위치
            const windowHeight = window.innerHeight; // 화면 높이
            const documentHeight = document.documentElement.scrollHeight; // 전체 문서 높이

            // 스크롤이 80% 이상일 때
            if (scrollTop + windowHeight >= documentHeight * 0.8 && !this.loading && this.feeds.current_page < this.feeds.last_page) {
                const nextPage = this.feeds.current_page + 1;
                this.getList(page);
            }
        },
        scrollBottom(){
            this.$nextTick(() => {
                const modalContent = document.querySelector(".modal-body");
                modalContent.scrollTo({
                    top: modalContent.scrollHeight,
                    behavior: "smooth",
                });
            });
        },
        clickTab(activityType){
            this.selectedActivityType = activityType.key;
            this.getList(1);
        },
        async getList(page){
            if(page == 1){
                this.list.data = [];
            }

            let response = {};
            switch (this.selectedActivityType) {
                case 'post':
                    response = await this.fetchPosts(page);
                    break;
                case 'reply':
                    response = await this.fetchReplies(page);
                    break;
                case 'quotation':
                    response = await this.fetchQuotation(page);
                    break;
            }

            // 기존 데이터에 새 데이터를 추가
            this.list.data = [...this.list.data, ...response.data.data];
            this.list.current_page = response.data.current_page;
            this.list.last_page = response.data.last_page;
            this.list.total = response.data.total;

            console.log(this.list);
        },
        async _follow(userId) {
            const response = await sendRequest('post', '/api/follows', { user_id: userId });
            this.follow.is_following = true;
        },
        async _unFollow(userId) {
            const response = await sendRequest('delete', '/api/follows/' + userId);
            this.follow.is_following = false;
        }
    },
}).mount("#app");
