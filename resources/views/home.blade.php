@vite(['resources/js/pages/home.js'])
<x-app-layout>
    <post-form-component></post-form-component>

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
    <x-footer></x-footer>
</x-app-layout>
