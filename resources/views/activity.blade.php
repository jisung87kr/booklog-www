@vite(['resources/js/pages/activity.js'])
<x-app-layout>
    <div id="app" class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center mt-3 md:mt-0">
            <div class="overflow-x-auto">
                <div class="flex flex-nowrap gap-3">
                    <template v-for="(activityType, idx) in activityTypes"
                              :key="idx"
                    >
                        <button type="button"
                                class="shrink-0 px-3.5 py-2 border rounded-lg font-medium hover:bg-gray-200"
                                :class="activityTypeSelected == activityType.key ? 'bg-gray-200' : ''"
                                @click="clickTab(activityType)"
                        >@{{activityType.value}}</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-3">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                <template v-if="activityTypeSelected == 'follow'">
                    <follower-component v-for="follow in list.data"
                                    :key="follow.id"
                                    :follow="follow"
                                    :auth="auth"
                                    class="p-4"
                    ></follower-component>
                </template>
                <template v-else-if="activityTypeSelected == 'reply'">
                    <feed-component :feed="feed"
                                    v-for="feed in list.data"
                                    :key="feed.id"
                                    :auth="auth"
                                    class="p-4"
                                    @open-comment-modal="showContentModal"
                    ></feed-component>
                </template>
                <template v-else-if="activityTypeSelected == 'mention'">
                    <feed-component :feed="feed"
                                    v-for="feed in list.data"
                                    :key="feed.id"
                                    :auth="auth"
                                    class="p-4"
                                    @open-comment-modal="showContentModal"
                    ></feed-component>
                </template>
                <template v-else-if="activityTypeSelected == 'quotation'">
                    <feed-component :feed="feed"
                                    v-for="feed in list.data"
                                    :key="feed.id"
                                    :auth="auth"
                                    class="p-4"
                                    @open-comment-modal="showContentModal"
                    ></feed-component>
                </template>
            </div>
        </div>
    </div>
    <x-footer></x-footer>
</x-app-layout>
