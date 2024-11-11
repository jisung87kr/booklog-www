@vite(['resources/js/pages/search.js'])
<x-app-layout>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center">
            <div class="bg-white sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                <form action="" class="p-6">
                    <div class="relative pl-6 border rounded-lg w-full">
                        <button type="button" class="absolute left-4 top-2.5 -translate-x-1/2 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                        </button>
                        <input type="text" name="q" class="border-none w-full focus:border-none" placeholder="검색" v-model="q" @keyup="search()">
                        <input type="hidden" name="qsearch_type" class="border-none w-full focus:border-none" placeholder="검색" v-model="qsearch_type">
                    </div>
                </form>
                <template v-if="q == ''">
                    <div class="opacity-60 font-medium px-6">팔로우 추천</div>
                    <div class="divide-y">
                        <template v-for="user in recommendedUsers" :key="user.id">
                            <avatar-component :user="user">
                                <template v-slot:follower-count>
                                    <div class="mt-3 text-sm">팔로워 <span v-html="user.followers_count"></span>명</div>
                                </template>
                            </avatar-component>
                        </template>
                    </div>
                </template>
                <template v-else>
                    <template v-if="feeds.data.length > 0">
                        <feed-component v-for="feed in feeds.data"
                                        :feed="feed"
                                        :key="feed.id"
                                        :auth="auth"
                                        class="p-4"
                                        @open-comment-modal="showContentModal"
                        ></feed-component>
                    </template>
                    <template v-else>
                        <div class="p-4 pt-0">검색 결과가 없습니다.</div>
                    </template>
                </template>
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
                        <comment-form :model="selectedFeed" @stored-comment="scrollBottom"></comment-form>
                    </div>
                </div>
            </template>
        </modal-component>
    </div>
    <x-footer></x-footer>
</x-app-layout>
