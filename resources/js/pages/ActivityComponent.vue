<template>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center mt-3 md:mt-0">
            <div class="overflow-x-auto">
                <div class="flex flex-nowrap gap-3">
                    <template v-for="(activityType, idx) in activityTypes"
                              :key="idx"
                    >
                        <button type="button"
                                class="shrink-0 px-3.5 py-2 border rounded-lg font-medium hover:bg-gray-200"
                                :class="selectedActivityType == activityType.key ? 'bg-gray-200' : ''"
                                @click="clickTab(activityType)"
                        >{{activityType.value}}</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-3">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6 shadow">
                <template v-if="list.data.length > 0">
                    <template v-if="selectedActivityType == 'follow'">
                        <follower-component v-for="follow in list.data"
                                            :key="follow.id"
                                            :follow="follow"
                                            :auth="auth"
                                            class="p-4"
                        ></follower-component>
                    </template>
                    <template v-else-if="selectedActivityType == 'reply'">
                        <comment-component v-for="comment in list.data"
                                           :key="comment.id"
                                           :comment="comment"
                                           :auth="auth"
                                           :feed="comment.commentable"
                                           class="p-4"
                        >
                        </comment-component>
                    </template>
                    <template v-else-if="selectedActivityType == 'mention'">
                        <feed-component v-for="mention in list.data"
                                        :key="mention.id"
                                        :auth="auth"
                                        class-name="p-4"
                                        :feed="mention.post"></feed-component>
                    </template>
                    <template v-else-if="selectedActivityType == 'quotation'">
                        <feed-component v-for="quotation in list.data"
                                        :key="quotation.id"
                                        :auth="auth"
                                        class-name="p-4"
                                        :feed="quotation"></feed-component>
                    </template>
                </template>
                <template v-else>
                    <div class="p-6 text-gray-500 text-sm font-bold">데이터가 존재하지 않습니다.</div>
                </template>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            // 쿼리스트리 q의 값 로드
            selectedActivityType: 'follow',
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
                const response = await axios.get('/api/user');
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchFollowers() {
            try {
                const response = await axios.get(`/api/users/${this.auth.username}/activity/followers`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchReplies() {
            try {
                const response = await axios.get(`/api/users/${this.auth.username}/activity/replies`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchMentions() {
            try {
                const response = await axios.get(`/api/users/${this.auth.username}/activity/mentions`);
                return response.data;
            } catch (error) {
                return null;
            }
        },
        async fetchQuotation() {
            try {
                const response = await axios.get(`/api/users/${this.auth.username}/activity/quotations`);
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
}
</script>
