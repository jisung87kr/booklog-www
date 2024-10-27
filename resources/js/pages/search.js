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

createApp({
    components: {
        "avatar-component": AvatarComponent,
        "feed-component": FeedComponent,
        "modal-component": ModalComponent,
        "comment-list": CommentListComponent,
        "comment-form": CommentForm,
        "like-button": LikeButton,
        "share-button": ShareButton,
    },
    data() {
        return {
            // 쿼리스트리 q의 값 로드
            q: new URLSearchParams(window.location.search).get('q') || '',
            qsearch_type: new URLSearchParams(window.location.search).get('qsearch_type') || '',
            recommendedUsers: [],
            auth: null,
            feeds: {
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
        this.recommendedUsers = await this.fetchRecommendedUsers();
        window.addEventListener("scroll", this.handleScroll);
        this.auth = await this.fetchUser();
        if(this.q){
            const feedsResponse = await this.fetchFeeds();
            this.feeds = feedsResponse;
            console.log(this.feed);
        }
    },
    methods: {
        async fetchUser() {
            try {
                const response = await axios.get('/api/user');
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchRecommendedUsers() {
            this.loading = true;
            const response = await sendRequest('GET', '/api/recommend/users');
            return response.data;
        },
        async fetchFeeds(page = 1) {
            try {
                this.loading = true;
                let params = {
                    page: page,
                    q: this.q,
                    qsearch_type: this.qsearch_type,
                }

                let response = await sendRequest('GET', `/api/feeds`, params);
                return response.data;
            } catch (error) {
                alert(error.message);
            } finally {
                this.loading = false;
            }
        },
        async handleScroll() {
            // 현재 스크롤 위치 계산
            const scrollTop = window.scrollY; // 스크롤 위치
            const windowHeight = window.innerHeight; // 화면 높이
            const documentHeight = document.documentElement.scrollHeight; // 전체 문서 높이

            // 스크롤이 80% 이상일 때
            if (scrollTop + windowHeight >= documentHeight * 0.8 && !this.loading && this.feeds.current_page < this.feeds.last_page) {
                const nextPage = this.feeds.current_page + 1;
                const feedsResponse = await this.fetchFeeds(nextPage);

                // 기존 데이터에 새 데이터를 추가
                this.feeds.data = [...this.feeds.data, ...feedsResponse.data.data];
                this.feeds.current_page = feedsResponse.data.current_page;
                this.feeds.last_page = feedsResponse.data.last_page;
                this.feeds.total = feedsResponse.data.total;
            }
        },
        showContentModal(feed){
            this.contentModalOpen = true;
            this.selectedFeed = feed;
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
        async search(){
            this.feeds = await this.fetchFeeds();
        }
    },
}).mount("#app");
