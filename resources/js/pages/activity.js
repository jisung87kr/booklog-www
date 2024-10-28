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
            activityTypeSelected: 'follow',
            activityTypes: [
                {key: 'follow', value: '팔로우'},
                {key: 'reply', value: '답글'},
                {key: 'mention', value: '언급'},
                {key: 'quotation', value: '인용'},
            ],
            q: new URLSearchParams(window.location.search).get('q') || '',
            qsearch_type: new URLSearchParams(window.location.search).get('qsearch_type') || '',
            auth: null,
            list: {
                current_page: 1,
                data: [
                    {id: 1, user: {name: "name", profile_photo_url: "https://via.placeholder.com/150"}, note: "note", images: []},
                    {id: 2, user: {name: "name", profile_photo_url: "https://via.placeholder.com/150"}, note: "note", images: []},
                    {id: 2, user: {name: "name", profile_photo_url: "https://via.placeholder.com/150"}, note: "note", images: []},
                ],
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
        this.getList(1);
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
        async fetchFollowers() {
            try {
                const response = await axios.get('/api/activity/followers');
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchReplies() {
            try {
                const response = await axios.get('/api/activity/replies');
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchMentions() {
            try {
                const response = await axios.get('/api/activity/mentions');
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchQuotation() {
            try {
                const response = await axios.get('/api/activity/quotations');
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
            this.activityTypeSelected = activityType.key;
            this.getList(1);
        },
        async getList(page){
            let response = {};
            switch (this.activityTypeSelected) {
                case 'follow':
                    response = await this.fetchFollowers(page);
                    break;
                case 'reply':
                    response = await this.fetchReplies(page);
                    break;
                case 'mention':
                    response = await this.fetchMentions(page);
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
        },
    },
}).mount("#app");
