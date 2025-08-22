<script setup>
import { ref, onMounted, onBeforeUnmount, defineEmits } from 'vue';
import {wrapWithSpan} from "../common.js";
import {useUserStore} from "../stores/user.js";
import BookcaseComponent from "./BookcaseComponent.vue";

const userStore = useUserStore();
const auth = ref(userStore.user);
const props = defineProps({
    feed: {
        type: Object,
        required: true,
    },
});

console.log(props);
</script>
<template>
    <!-- Modern feed card with enhanced design -->
    <div :class="['bg-white border-b sm:border-0 border-gray-100 sm:border-gray-200 transition-all duration-300 hover:shadow-lg', $attrs.class]">
        <div class="p-4 sm:p-6">
            <!-- Header Section -->
            <div class="flex items-start justify-between mb-3 sm:mb-4">
                <div class="flex items-center space-x-3 min-w-0 flex-1">
                    <div class="relative shrink-0">
                        <div class="relative">
                            <img class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl ring-2 ring-blue-50 hover:ring-blue-100 transition-all duration-200 object-cover"
                                 :src="(feed.user && feed.user.profile_photo_url) ?? 'https://www.gravatar.com/avatar/'"
                                 :alt="feed.user?.username">
                            <!-- Enhanced online indicator -->
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full border-3 border-white shadow-lg flex items-center justify-center">
                                <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <a :href="'/@'+feed.user.username" v-if="feed.user"
                               class="font-bold text-gray-900 hover:text-blue-600 transition-colors text-sm sm:text-base truncate">
                                {{ feed.user.username }}
                            </a>
                            <span v-else class="font-bold text-gray-900 text-sm sm:text-base">북로그</span>
                            <!-- Verified badge -->
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.243.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <!-- Enhanced timestamp with relative time -->
                        <div class="flex items-center space-x-2 text-xs sm:text-sm text-gray-500">
                            <span v-html="feed.created_at_human"></span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>공개</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Touch-friendly dropdown button -->
                <dropdown-component v-if="auth" class="shrink-0">
                    <template v-slot:mybutton>
                        <button type="button" class="p-2.5 sm:p-2 rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors duration-200 touch-manipulation">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                            </svg>
                        </button>
                    </template>

                    <!-- Mobile-optimized dropdown menu -->
                    <div class="dropdown w-56 sm:w-60 bg-white border border-gray-200 rounded-2xl shadow-xl absolute right-0 top-12 z-50 py-2">
                        <user-action-button actionable-type="post" action-name="bookmark" :model="feed" :auth="auth"
                                          class="w-full px-4 py-4 sm:py-3 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 flex items-center space-x-3 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span>저장{{ feed && feed.bookmark_id ? ' 취소' : '' }}</span>
                        </user-action-button>

                        <user-action-button actionable-type="post" action-name="uninterested" :model="feed" :auth="auth"
                                          class="w-full px-4 py-4 sm:py-3 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 flex items-center space-x-3 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                            </svg>
                            <span>관심없음{{ feed && feed.uninterested_id ? ' 취소' : '' }}</span>
                        </user-action-button>

                        <div class="border-t border-gray-100 my-2"></div>

                        <user-action-button actionable-type="post" action-name="block" :model="feed" :auth="auth"
                                          class="w-full px-4 py-4 sm:py-3 text-sm text-red-600 hover:bg-red-50 active:bg-red-100 flex items-center space-x-3 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                            </svg>
                            <span>차단하기{{ feed && feed.block_id ? ' 취소' : '' }}</span>
                        </user-action-button>

                        <user-action-button v-if="auth.id !== feed.user.id" actionable-type="post" action-name="report" :model="feed" :auth="auth"
                                          class="w-full px-4 py-4 sm:py-3 text-sm text-red-600 hover:bg-red-50 active:bg-red-100 flex items-center space-x-3 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <span>신고하기{{ feed && feed.report_id ? ' 취소' : '' }}</span>
                        </user-action-button>

                        <div class="border-t border-gray-100 my-2"></div>

                        <user-action-button actionable-type="post" action-name="share" :model="feed" :auth="auth"
                                          class="w-full px-4 py-4 sm:py-3 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 flex items-center space-x-3 transition-colors touch-manipulation">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            <span>링크복사</span>
                        </user-action-button>
                    </div>
                </dropdown-component>
            </div>

            <!-- Content Section -->
            <template v-if="feed.type === 'bookcase'">
                <div class="mb-4">
                    <bookcase-component :bookcase="feed.bookcase" sortable="false" :dropdown="false"></bookcase-component>
                </div>
            </template>
            <template v-else>
                <div class="mb-4">
                    <!-- Content with better mobile typography -->
                    <div class="text-gray-800 text-sm sm:text-base leading-relaxed feed-content mb-4" v-html="wrapWithSpan(feed.content)"></div>

                    <!-- Images with mobile optimization -->
                    <template v-if="feed.images.length > 0">
                        <div class="mb-4 -mx-3 sm:mx-0 sm:rounded-xl overflow-hidden">
                            <swiper-component :images="feed.images"></swiper-component>
                        </div>
                    </template>
                    <!-- Enhanced Action Buttons with better design -->
                    <div class="pt-4 sm:pt-5 border-t border-gray-50">
                        <div class="flex items-center justify-between w-full">
                            <!-- Enhanced engagement buttons -->
                            <div class="flex items-center space-x-4 sm:space-x-6">
                                <like-button :model="feed" :auth="auth" type="post"
                                             class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-red-50 active:bg-red-100 transition-colors touch-manipulation"></like-button>
                                <comment-button :model="feed" type="post"
                                                class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-blue-50 active:bg-blue-100 transition-colors touch-manipulation"></comment-button>
                                <share-button :model="feed" type="post"
                                              class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-green-50 active:bg-green-100 transition-colors touch-manipulation"></share-button>
                            </div>

                            <!-- Bookmark button -->
                            <div class="flex items-center">
                                <button type="button" class="p-2.5 rounded-full hover:bg-gray-50 active:bg-gray-100 transition-colors touch-manipulation">
                                    <svg class="w-5 h-5 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<style scoped>
/* Enhanced feed content styling */
.feed-content .tagbox{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Enhanced profile image hover effect */
.relative img:hover {
    transform: scale(1.05);
}

/* Animated pulse for online indicator */
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Enhanced button interactions */
.hover\:bg-red-50:hover {
    background-color: rgb(254 242 242);
}

.hover\:bg-blue-50:hover {
    background-color: rgb(239 246 255);
}

.hover\:bg-green-50:hover {
    background-color: rgb(240 253 244);
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .touch-manipulation {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        min-height: 44px;
        min-width: 44px;
    }

    /* Hide scrollbars on mobile for stories */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Improve touch targets */
    .dropdown .user-action-button {
        min-height: 48px;
    }

    /* Better spacing on mobile */
    .space-x-4 > * + * {
        margin-left: 1rem;
    }
}

/* Enhanced transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Ensure smooth scrolling */
.scrollbar-hide {
    scroll-behavior: smooth;
}

/* Enhanced card hover effect */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
