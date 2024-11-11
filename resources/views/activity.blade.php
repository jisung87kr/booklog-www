@vite(['resources/js/pages/activity.js'])
<x-app-layout>
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
                        >@{{activityType.value}}</button>
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
    <x-footer></x-footer>
</x-app-layout>
