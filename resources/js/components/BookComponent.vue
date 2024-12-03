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
            <div class="border rounded-lg overflow-hidden mb-2">
                <img src="https://placehold.co/600x400" alt="">
            </div>
        </slot>
        <slot name="body">
            <div class="line-clamp-1 font-bold">{{props.book.title}}</div>
            <div class="line-clamp-1 text-sm text-gray-600">{{props.book.author}}</div>
        </slot>
        <slot name="footer"></slot>
    </div>
</template>
