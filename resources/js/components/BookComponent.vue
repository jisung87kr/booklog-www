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
    <div class="book-wrapper">
        <slot name="header">
            <div class="border rounded-lg overflow-hidden mb-2 aspect-square">
                <a :href="props.book.link" target="_blank">
                    <img :src="props.book.cover_image" :alt="props.book.title" class="w-full h-full object-cover">
                </a>
            </div>
        </slot>
        <slot name="body">
            <a :href="props.book.link" target="_blank">
                <div class="line-clamp-1 font-bold">{{props.book.title}}</div>
                <div class="line-clamp-1 text-sm text-gray-600">{{props.book.author}}</div>
            </a>
        </slot>
        <slot name="footer"></slot>
    </div>
</template>
