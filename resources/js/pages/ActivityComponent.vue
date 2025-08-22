<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useUserStore } from "../stores/user.js";
import DropdownComponent from "../components/DropdownComponent.vue";
import HeaderComponent from "../components/headerComponent.vue";

// 사용자 인증 스토어 사용 설정
const userStore = useUserStore();
//await userStore.checkUser();
const auth = ref(userStore.user);
const loaded = ref(false);
const selectedActivityType = ref('follow');
const activityTypes = [
    { key: 'follow', value: '팔로우' },
    { key: 'reply', value: '답글' },
    { key: 'mention', value: '언급' },
    { key: 'quotation', value: '인용' },
];

const q = ref(new URLSearchParams(window.location.search).get('q') || '');
const qsearch_type = ref(new URLSearchParams(window.location.search).get('qsearch_type') || '');
const list = ref({
    current_page: 1,
    data: [],
    last_page: null,
    total: null,
});
const loading = ref(false);
const contentModalOpen = ref(false);
const selectedFeed = ref({
    id: null,
    user: {
        name: null,
        profile_photo_url: null,
    },
    note: null,
    images: [],
});

const fetchFollowers = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/followers`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchReplies = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/replies`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchMentions = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/mentions`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};

const fetchQuotation = async () => {
    try {
        const response = await axios.get(`/api/users/${userStore.user.username}/activity/quotations`);
        return response.data;
    } catch (error) {
        console.error(error);
        return null;
    }
};


const handleScroll = async () => {
    const scrollTop = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    if (scrollTop + windowHeight >= documentHeight * 0.8 && !loading.value && list.value.current_page < list.value.last_page) {
        const nextPage = list.value.current_page + 1;
        await getList(nextPage);
    }
};

const clickTab = (activityType) => {
    selectedActivityType.value = activityType.key;
    getList(1);
};

const getList = async (page) => {
    if (page === 1) {
        list.value.data = [];
    }

    let response = {};
    switch (selectedActivityType.value) {
        case 'follow':
            response = await fetchFollowers();
            break;
        case 'reply':
            response = await fetchReplies();
            break;
        case 'mention':
            response = await fetchMentions();
            break;
        case 'quotation':
            response = await fetchQuotation();
            break;
    }

    list.value.data = [...list.value.data, ...response.data.data];
    list.value.current_page = response.data.current_page;
    list.value.last_page = response.data.last_page;
    list.value.total = response.data.total;
};

onMounted(async () => {
    window.addEventListener("scroll", handleScroll);
    await getList(1);
    loaded.value = true;
});

onBeforeUnmount(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <Transition name="slide-fade">
        <div v-if="loaded" class="min-h-screen bg-gray-50">
            <!-- Modern header -->
            <header-component class="sticky top-0 z-30 bg-white border-b border-gray-200 !max-w-full px-2 md:px-0 mb-6">
                <div class="flex justify-between items-center max-w-2xl w-full">
                    <history-back-button class="touch-manipulation"></history-back-button>
                    <div class="flex items-center space-x-2">
                        <div class="font-bold text-gray-900">활동</div>
                        <dropdown-component class="relative">
                            <template #mybutton>
                                <button type="button" class="rounded-full border border-gray-300 w-9 h-9 sm:w-8 sm:h-8 bg-white hover:bg-gray-50 active:bg-gray-100 transition-colors touch-manipulation flex items-center justify-center min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2">
                                        <path d="M6 9l6 6l6 -6"></path>
                                    </svg>
                                </button>
                            </template>
                            <div class="dropdown absolute left-1/2 -translate-x-1/2 top-10 w-56 sm:w-48 border border-gray-200 rounded-2xl bg-white shadow-xl py-2 z-50">
                                <template v-for="(activityType, idx) in activityTypes" :key="idx">
                                    <button type="button"
                                            class="w-full px-4 py-4 sm:py-3 hover:bg-gray-50 active:bg-gray-100 flex justify-between items-center transition-colors touch-manipulation text-left min-h-[48px] sm:min-h-0"
                                            :class="selectedActivityType === activityType.key ? 'text-blue-600 bg-blue-50' : 'text-gray-700'"
                                            @click="clickTab(activityType)">
                                        <span class="font-medium">{{activityType.value}}</span>
                                        <svg v-if="selectedActivityType === activityType.key"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2" class="text-blue-600">
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </dropdown-component>
                    </div>
                    <div class="w-6"></div>
                </div>
            </header-component>

            <div class="container-fluid mx-auto w-full px-0 sm:px-4 pb-20 sm:pb-8">
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl">
                        <!-- Modern activity container -->
                        <div class="bg-white border-b sm:border sm:rounded-2xl sm:shadow-sm overflow-hidden">
                            <!-- Activity type indicator -->
                            <div class="px-4 sm:px-6 py-4 sm:py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5l-5-5h5v-5a7.5 7.5 0 0 0-15 0v5"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 transition-colors">
                                                {{ activityTypes.find(type => type.key === selectedActivityType)?.value }}
                                            </h2>
                                            <p class="text-sm text-gray-600">최근 활동을 확인하세요</p>
                                        </div>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-500 bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm">
                                        {{ list.total || 0 }}개
                                    </div>
                                </div>
                            </div>

                            <!-- Activity content -->
                            <div class="min-h-[300px]">
                                <template v-if="list.data.length > 0">
                                    <div class="divide-y divide-gray-100">
                                        <template v-if="selectedActivityType == 'follow'">
                                            <follower-component v-for="follow in list.data"
                                                                :key="follow.id"
                                                                :follow="follow"
                                                                :auth="auth"
                                                                class="p-4 sm:p-6 hover:bg-gray-50 transition-colors"
                                            ></follower-component>
                                        </template>
                                        <template v-else-if="selectedActivityType == 'reply'">
                                            <comment-component v-for="comment in list.data"
                                                               :key="comment.id"
                                                               :comment="comment"
                                                               :auth="auth"
                                                               :feed="comment.commentable"
                                                               class="p-4 sm:p-6 hover:bg-gray-50 transition-colors"
                                            ></comment-component>
                                        </template>
                                        <template v-else-if="selectedActivityType == 'mention'">
                                            <feed-component v-for="mention in list.data"
                                                            :key="mention.id"
                                                            :auth="auth"
                                                            :feed="mention.post"
                                            ></feed-component>
                                        </template>
                                        <template v-else-if="selectedActivityType == 'quotation'">
                                            <feed-component v-for="quotation in list.data"
                                                            :key="quotation.id"
                                                            :auth="auth"
                                                            :feed="quotation"
                                            ></feed-component>
                                        </template>
                                    </div>
                                </template>

                                <!-- Enhanced empty state -->
                                <template v-else>
                                    <div class="p-8 sm:p-12 text-center">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">아직 활동이 없습니다</h3>
                                        <p class="text-gray-500">{{ activityTypes.find(type => type.key === selectedActivityType)?.value }} 활동이 있을 때 여기에 표시됩니다.</p>
                                    </div>
                                </template>

                                <!-- Loading indicator -->
                                <template v-if="loading">
                                    <div class="p-8 text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                                        <p class="text-sm text-gray-500 mt-2">로딩 중...</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Mobile optimizations */
@media (max-width: 640px) {
    .touch-manipulation {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }

    /* Ensure touch targets are at least 44px */
    .touch-manipulation {
        min-height: 44px;
        min-width: 44px;
    }

    /* Mobile dropdown adjustments */
    .dropdown {
        max-width: calc(100vw - 2rem);
        left: 50% !important;
        transform: translateX(-50%);
    }

    /* Better mobile spacing */
    .space-x-2 > * + * {
        margin-left: 0.5rem;
    }
}

/* Enhanced transitions */
.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Gradient animations */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradient-shift 6s ease infinite;
}

.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradient-rotate 8s ease infinite;
}

@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes gradient-rotate {
    0% { background-position: 0% 50%; }
    25% { background-position: 100% 0%; }
    50% { background-position: 100% 100%; }
    75% { background-position: 0% 100%; }
    100% { background-position: 0% 50%; }
}

/* Enhanced hover effects */
.hover\\:bg-gray-50:hover {
    background-color: rgba(249, 250, 251, 0.8);
    backdrop-filter: blur(1px);
}

/* Improved scrolling */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E0 transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #CBD5E0;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #A0AEC0;
}

/* Activity card hover effect */
.activity-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.activity-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Loading spinner enhancement */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Icon bounce animation */
.icon-bounce {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translateY(0);
    }
    40%, 43% {
        transform: translateY(-5px);
    }
    70% {
        transform: translateY(-2px);
    }
    90% {
        transform: translateY(-1px);
    }
}

/* Activity type transition */
.activity-transition {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
