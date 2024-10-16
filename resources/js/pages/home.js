import { createApp } from "vue";
import axios from "axios";
import FeedComponent from "../components/FeedComponent.vue";

createApp({
    components: {
        "feed-component": FeedComponent,
    },
    data() {
        return {
            feeds: {
                current_page: 1,
                data: [],
                last_page: null,
                total: null,
            },
            loading: false,
            modalOpen: false,
            contentModalOpen: true,
            currentFeed: {
                id: null,
                user: {
                    name: null,
                    profile_photo_url: null,
                },
                note: null,
                images: [],
                comments: [],
            },
        };
    },
    async mounted() {
        window.addEventListener("scroll", this.handleScroll);
        const feedsResponse = await this.fetchFeeds();
        this.feeds = feedsResponse.data;
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.handleScroll);
    },
    methods: {
        async fetchFeeds(page = 1) {
            try {
                this.loading = true;
                let response = await axios({
                    method: "get",
                    url: `/api/feeds?page=${page}`,
                });

                if (response.data.status !== true) {
                    throw new Error(response.data.message);
                }

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
    },
}).mount("#app");
