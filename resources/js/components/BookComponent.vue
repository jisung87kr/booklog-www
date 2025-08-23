<script setup>
import { ref, defineProps } from 'vue';
const props = defineProps({
    book: {
        type: Object,
        required: true,
    }
});
</script>
<template>
    <div class="book-wrapper group relative h-full bg-white">
        <slot name="header">
            <div class="relative overflow-hidden mb-3 bg-gray-100 rounded-xl shadow-sm aspect-[3/4.5] ">
                <a :href="props.book.link" target="_blank" class="block w-full h-full">
                    <img
                        :src="props.book.cover_image || 'https://placehold.co/300x450/f3f4f6/9ca3af?text=No+Cover'"
                        :alt="props.book.title"
                        class="object-cover transition-transform duration-300 group-hover:scale-105 w-full h-full"
                        loading="lazy">
                </a>
                <!-- Overlay gradient for better text readability if needed -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>
        </slot>

        <slot name="body">
            <div class="px-2 pb-2 flex flex-col justify-between">
                <a :href="props.book.link" target="_blank" class="block hover:text-blue-600 transition-colors flex-1">
                    <!-- Fixed height container for title -->
                    <div class="h-10 sm:h-12 mb-1">
                        <h3 class="font-bold text-gray-900 text-sm sm:text-base line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors h-full flex items-start">
                            {{ props.book.title }}
                        </h3>
                    </div>

                    <!-- Fixed height container for author -->
                    <div class="h-4 sm:h-5 mb-1">
                        <p class="text-xs sm:text-sm text-gray-600 line-clamp-1 h-full flex items-center">
                            {{ props.book.author }}
                        </p>
                    </div>

                    <!-- Fixed height container for publisher -->
                    <div class="h-4 sm:h-5" v-if="props.book.publisher">
                        <p class="text-xs text-gray-500 line-clamp-1 h-full flex items-center">
                            {{ props.book.publisher }}
                        </p>
                    </div>
                    <div class="h-4 sm:h-5" v-else>
                        <!-- Empty space to maintain consistent height -->
                    </div>
                </a>
            </div>
        </slot>

        <slot name="footer"></slot>

        <!-- Subtle border on hover -->
        <div class="absolute inset-0 border-2 border-blue-200 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
    </div>
</template>

