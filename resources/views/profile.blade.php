<script>
    window.userData = @json($user);
</script>
@vite(['resources/js/pages/profile.js'])
<x-app-layout>
    <div class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center mt-3 md:mt-0">
            <div class="bg-white shadow w-full rounded-2xl max-w-xl">
                <div class="p-6">
                    <div class="flex justify-between">
                        <div class="mr-3">
                            <div class="text-3xl font-bold" v-html="user.name"></div>
                            <div v-html="user.username"></div>
                        </div>
                        <div>
                            <img :src="user.profile_photo_url" alt="" class="w-20 h-20 rounded-full bg-red border">
                        </div>
                    </div>
                    <div class="mt-3"
                         v-if="user.description"
                         v-html="user.description"
                    >
                    </div>
                    <div class="flex justify-between items-center mt-3">
                        <div class="text-sm text-zinc-500 flex">
                            <div>팔로워 <span v-html="user.followers_count"></span>명</div>
                            <template v-if="user.user_link">
                                <span class="px-1">∙</span><a :href="user.user_link" target="_blank" v-html="user.user_link"></a>
                            </template>
                        </div>
                        <template v-if="auth && user.id != auth.id">
                            <dropdown-component>
                                <template v-slot:mybutton>
                                    <button type="button" class="rounded-full border border-zinc-900 w-6 h-6 text-center flex justify-center items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="1">
                                            <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                            <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                        </svg>
                                    </button>
                                </template>
                                <ul class="dropdown w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10">
                                    <li>
                                        <user-action-button actionable-type="user"
                                                            action-name="share"
                                                            :model="user"
                                                            :auth="auth"
                                                            class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                        >
                                            <span>링크복사</span>
                                        </user-action-button>
                                    </li>
                                    <li>
                                        <user-action-button actionable-type="user"
                                                             action-name="show_profile"
                                                             :model="user"
                                                             :auth="auth"
                                                             class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                        >
                                            <span>이 프로필 정보</span>
                                        </user-action-button>
                                    </li>
                                    <li>
                                        <user-action-button actionable-type="user"
                                                            action-name="block"
                                                            :model="user"
                                                            :auth="auth"
                                                            class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                        >
                                            <span>차단하기</span><span v-if="user && user.block_id"> 취소</span>
                                        </user-action-button>
                                    </li>
                                    <li>
                                        <user-action-button actionable-type="user"
                                                            action-name="report"
                                                            :model="user"
                                                            :auth="auth"
                                                            class-name="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"
                                        >
                                            <span>신고하기</span><span v-if="user && user.report_id"> 취소</span>
                                        </user-action-button>
                                    </li>
                                </ul>
                            </dropdown-component>

                        </template>
                    </div>
                    <template v-if="auth && user.id == auth.id">
                        <button type="button" class="rounded-lg px-3 py-2 border border-zinc-300 text-sm font-bold w-full mt-6">프로필 수정</button>
                    </template>
                    <template v-else>
                        <div class="grid grid-cols-2 gap-4 my-6">
{{--                            <button type="button"--}}
{{--                                    class="rounded-lg px-3 py-2 bg-zinc-800 text-white text-sm font-bold"--}}
{{--                                    v-if="user.is_following == false"--}}
{{--                            >팔로우</button>--}}
{{--                            <button type="button"--}}
{{--                                    class="rounded-lg px-3 py-2 bg-zinc-800 text-white text-sm font-bold"--}}
{{--                                    v-if="user.is_following == true"--}}
{{--                            >팔로잉</button>--}}
                            <follow-toggle-button :user="user"></follow-toggle-button>
                            <button type="button"
                                    class="rounded-lg px-3 py-2 border border-zinc-300 text-sm font-bold"
                            >언급</button>
                        </div>
                    </template>
                </div>
                <div class="grid grid-cols-3">
                    <template v-for="type in activityTypes"
                              :key="type.key">
                        <button type="button"
                                class="text-sm px-3 py-2 font-bold border-b"
                                :class="type.key == selectedActivityType ? 'border-zinc-900 text-zinc-900' : 'border-zinc-300 text-zinc-500'"
                                @click="clickTab(type)"
                        >@{{type.value}}</button>
                    </template>
                </div>
                <template v-if="list.data.length > 0">
                    <div class="divide-y">
                        <template v-if="selectedActivityType == 'post'">
                            <feed-component v-for="post in list.data"
                                                :key="post.id"
                                                :feed="post"
                                                :auth="auth"
                                                class="p-6"
                            ></feed-component>
                        </template>
                        <template v-else-if="selectedActivityType == 'reply'">
                            <comment-component v-for="comment in list.data"
                                            :key="comment.id"
                                            :comment="comment"
                                            :auth="auth"
                                            class="p-6"
                            ></comment-component>
                        </template>
                        <template v-else-if="selectedActivityType == 'quotation'">
                            <feed-component v-for="quotation in list.data"
                                               :key="quotation.id"
                                               :feed="quotation"
                                               :auth="auth"
                                               class="p-6"
                            ></feed-component>
                        </template>
                    </div>
                </template>
                <template v-else>
                    <div class="p-6 text-gray-500 text-sm font-bold">데이터가 존재하지 않습니다.</div>
                </template>
            </div>
        </div>
    </div>
    <x-footer></x-footer>
</x-app-layout>
