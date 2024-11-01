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
                                :class="SelectedActivityType == activityType.key ? 'bg-gray-200' : ''"
                                @click="clickTab(activityType)"
                        >@{{activityType.value}}</button>
                    </template>
                </div>
            </div>
        </div>
        <div class="flex justify-center mt-3">
            <div class="bg-white divide-y sm:border sm:rounded-2xl flex-start max-w-xl w-full md:ms-6">
                <template v-if="SelectedActivityType == 'follow'">
                    <follower-component v-for="follow in list.data"
                                    :key="follow.id"
                                    :follow="follow"
                                    :auth="auth"
                                    class="p-4"
                    ></follower-component>
                </template>
                <template v-else-if="SelectedActivityType == 'reply'">
                    <comment-component v-for="comment in list.data"
                                        :key="comment.id"
                                        :comment="comment"
                                        :auth="auth"
                                        :feed="comment.commentable"
                                        class="p-4"
                    >
                    </comment-component>
                </template>
                <template v-else-if="SelectedActivityType == 'mention'">
                    <div class="p-4">
                        <feed-component v-for="metion in list.data"
                                        :key="metion.id"
                                        :auth="auth"
                                        :feed="metion.reading_process"></feed-component>
                    </div>
                </template>
                <template v-else-if="SelectedActivityType == 'quotation'">
                    <div class="p-4">
                        <feed-component v-for="metion in list.data"
                                        :key="metion.id"
                                        :auth="auth"
                                        :feed="metion.reading_process"></feed-component>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <x-footer></x-footer>
</x-app-layout>
