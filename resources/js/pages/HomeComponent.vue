<template>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                <feed-component :feed="feed"
                                v-for="feed in feeds.data"
                                :key="feed.id"
                                :auth="auth"
                                class="p-4"
                                @open-comment-modal="showContentModal"
                ></feed-component>
            </div>
            <div class="max-w-lg w-full mx-6 hidden lg:block">
                <div class="flex flex-col gap-6">
                    <div class="bg-white border rounded-2xl p-6">
                        <div>나를 위한 트랜드</div>
                    </div>
                    <div class="bg-white border rounded-2xl p-6">
                        <div>팔로우 추천</div>
                    </div>
                </div>
            </div>
        </div>
        <modal-component :is-visible="contentModalOpen"
                         @close="contentModalOpen = false"
        >
            <template v-slot:modal-header>
                <div class="p-3">
                    <div class="mb-3 font-bold">댓글</div>
                </div>
            </template>
            <div class="p-3">
                <div>
                    <comment-list :model="selectedFeed"
                                  :auth="auth"
                    ></comment-list>
                </div>
            </div>
            <template v-slot:modal-footer>
                <div class="p-3 border-t">
                    <div class="flex gap-2">
                        <like-button :auth="auth" :model="selectedFeed"></like-button>
                        <share-button :feed="selectedFeed"></share-button>
                    </div>
                    <div class="mt-1">
                        <div class="text-sm">좋아요 400개</div>
                    </div>
                    <div class="mt-3" v-if="auth">
                        <comment-form :model="selectedFeed"
                                      :auth="auth"
                                      @stored-comment="scrollBottom"
                        ></comment-form>
                    </div>
                </div>
            </template>
        </modal-component>
    </div>
</template>
<script>
export default {
    data() {
        return {
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
    async mounted() {
        window.addEventListener("scroll", this.handleScroll);
        this.auth = await this.fetchUser();
        const feedsResponse = await this.fetchFeeds();
        this.feeds = feedsResponse.data;
    },
    beforeUnmount() {
        window.removeEventListener("scroll", this.handleScroll);
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
    }
}
</script>
