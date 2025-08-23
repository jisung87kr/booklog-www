<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user.js';
import BookcaseComponent from "../components/BookcaseComponent.vue";
import BookComponent from "../components/BookComponent.vue";
import LikeButton from "../components/buttons/LikeButton.vue";
import CommentButton from "../components/buttons/CommentButton.vue";
import ShareButton from "../components//buttons/ShareButton.vue";
import HeaderComponent from "../components/headerComponent.vue";
import {sendRequest} from "../common.js";

const userStore = useUserStore();
const loaded = ref(false);
const auth = ref(userStore.user);
const bookcase = ref(window.__bookcase);
const profileUser = ref(window.__profileUser);

const deleteBookcase = () => {
    if (confirm('정말 삭제하시겠습니까?')) {
        sendRequest('delete', `/api/users/${auth.value.username}/bookcases/${bookcase.value.id}`)
            .then(() => {
                location.href = `/@${auth.value.username}`;
            });
    }
};
onMounted(async () => {
    loaded.value = true;
});
</script>
<template>
    <transition name="slide-fade">
        <div v-show="loaded" class="min-h-screen bg-gray-50">
            <!-- Modern header with gradient -->
            <header-component class="sticky top-0 z-30 bg-white border-b border-gray-200 !max-w-full px-2 md:px-0 mb-6">
                <div class="flex justify-between items-center w-full max-w-2xl">
                    <history-back-button class="touch-manipulation"></history-back-button>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="font-bold text-gray-900">@{{profileUser.username}}님의 책장</div>
                    </div>
                    <div class="w-6"></div>
                </div>
            </header-component>

            <div class="container-fluid max-w-2xl mx-auto w-full px-2 md:px-0 pb-20 sm:pb-8">
                <!-- Modern bookcase card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                    <!-- Bookcase header with gradient background -->
                    <div class="p-6 sm:p-8 bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 border-b border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0 mr-4">
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2 leading-tight">{{ bookcase.title }}</h1>
                                <p class="text-gray-600 leading-relaxed" v-if="bookcase.description">{{ bookcase.description }}</p>
                            </div>

                            <!-- Bookcase stats -->
                            <div class="shrink-0 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ bookcase.books?.length || 0 }}</div>
                                <div class="text-xs text-gray-500">권</div>
                            </div>
                        </div>

                        <!-- Action buttons for owner -->
                        <div v-if="auth && auth.id == profileUser.id" class="flex flex-col sm:flex-row gap-3 mb-4">
                            <a :href="'/@'+auth.username+'/bookcases/'+bookcase.id+'/edit'"
                               class="flex-1 px-4 py-3 sm:py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold rounded-xl text-center transition-colors touch-manipulation shadow-sm">
                                책장 수정
                            </a>
                            <button type="button"
                                    class="flex-1 px-4 py-3 sm:py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-semibold rounded-xl transition-colors touch-manipulation shadow-sm"
                                    @click="deleteBookcase">
                                삭제
                            </button>
                        </div>

                        <!-- Enhanced engagement buttons -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 sm:space-x-6">
                                <like-button :model="bookcase" :auth="auth" type="bookcase"
                                           class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-red-50 active:bg-red-100 transition-colors touch-manipulation"></like-button>
                                <comment-button :model="bookcase" type="bookcase"
                                              class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-blue-50 active:bg-blue-100 transition-colors touch-manipulation"></comment-button>
                                <share-button :model="bookcase" type="bookcase"
                                            class="flex items-center space-x-2 px-3 py-2 rounded-full hover:bg-green-50 active:bg-green-100 transition-colors touch-manipulation"></share-button>
                            </div>

                            <!-- Bookmark button -->
                            <button type="button" class="p-2.5 rounded-full hover:bg-gray-50 active:bg-gray-100 transition-colors touch-manipulation">
                                <svg class="w-5 h-5 text-gray-500 hover:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Books section -->
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">도서 목록</h2>
                                    <p class="text-sm text-gray-600">{{ bookcase.books?.length || 0 }}권의 책이 있습니다</p>
                                </div>
                            </div>
                        </div>

                        <template v-if="bookcase.books && bookcase.books.length > 0">
                            <!-- Books grid with enhanced design -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 auto-rows-fr">
                                <template v-for="book in bookcase.books" :key="book.id">
                                    <div class="group relative bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 flex flex-col">
                                        <book-component :book="book" class="flex-1 flex flex-col">
                                            <template #footer>
                                                <div class="p-3 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50/30">
                                                    <!-- Book stats if available -->
                                                    <div class="flex items-center justify-between mb-3 text-xs text-gray-500" v-if="book.likes_count || book.comments_count">
                                                        <div class="flex items-center space-x-3">
                                                            <span class="flex items-center space-x-1" v-if="book.likes_count">
                                                                <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <span>{{ book.likes_count }}</span>
                                                            </span>
                                                            <span class="flex items-center space-x-1" v-if="book.comments_count">
                                                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                                </svg>
                                                                <span>{{ book.comments_count }}</span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Action buttons -->
                                                    <div class="flex items-center justify-around">
                                                        <like-button :auth="auth" :model="book" type="book"
                                                                   class="p-1 rounded-full hover:bg-red-50 active:bg-red-100 transition-all duration-200 hover:scale-110 touch-manipulation"></like-button>
                                                        <comment-button :auth="auth" :model="book" type="book"
                                                                      class="p-1 rounded-full hover:bg-blue-50 active:bg-blue-100 transition-all duration-200 hover:scale-110 touch-manipulation"></comment-button>
                                                        <share-button :auth="auth" :model="book" type="book"
                                                                    class="p-1 rounded-full hover:bg-green-50 active:bg-green-100 transition-all duration-200 hover:scale-110 touch-manipulation"></share-button>

                                                        <!-- Bookmark button -->
                                                        <button type="button" class="p-2.5 rounded-full hover:bg-yellow-50 active:bg-yellow-100 transition-all duration-200 hover:scale-110 touch-manipulation">
                                                            <svg class="w-4 h-4 text-gray-500 hover:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </book-component>

                                        <!-- Floating favorite indicator -->
                                        <div class="absolute top-3 right-3 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300" v-if="book.is_favorite">
                                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template v-else>
                            <div class="text-center py-12 sm:py-16">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">책장이 비어있습니다</h3>
                                <p class="text-gray-500">아직 등록된 도서가 없습니다</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
/* Mobile optimizations */
@media (max-width: 640px) {
    .touch-manipulation {
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
        min-height: 44px;
        min-width: 44px;
    }

    /* Better spacing for mobile */
    .space-x-4 > * + * {
        margin-left: 1rem;
    }

    /* Mobile grid adjustments */
    .grid-cols-2 {
        gap: 1rem;
    }

    /* Reduce hover effects on mobile for better performance */
    .group:hover {
        transform: translateY(-4px);
    }

    /* Adjust book card animation delays for mobile */
    .grid > * {
        animation-delay: 0ms !important;
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
    animation: gradient 8s ease infinite;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Enhanced book card hover effects */
.group:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Staggered animation effect for book grid */
.grid > *:nth-child(1) { animation-delay: 0ms; }
.grid > *:nth-child(2) { animation-delay: 50ms; }
.grid > *:nth-child(3) { animation-delay: 100ms; }
.grid > *:nth-child(4) { animation-delay: 150ms; }
.grid > *:nth-child(5) { animation-delay: 200ms; }
.grid > *:nth-child(6) { animation-delay: 250ms; }

/* Book card entrance animation */
@keyframes bookEnter {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group {
    animation: bookEnter 0.4s ease-out forwards;
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

/* Smooth scrolling */
.overflow-y-auto {
    scroll-behavior: smooth;
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

/* Card shadow enhancements */
.hover\:shadow-lg:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Loading states */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Enhanced card interactions */
.group {
    position: relative;
    height: 100%;
}

/* Grid auto-rows for equal height cards */
.auto-rows-fr {
    grid-auto-rows: 1fr;
}

/* Ensure flex cards take full height */
.grid .group {
    display: flex;
    flex-direction: column;
    min-height: 100%;
}
</style>
