<script setup>
import { ref, onMounted, onBeforeUnmount, defineEmits } from 'vue';
const props = defineProps({
    images: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['delete-image']);
const images = ref(props.images);

const deleteImage = (idx) => {
    images.value.splice(idx, 1);
    emit('delete-image', idx);
}
</script>

<template>
    <div class="overflow-x-auto w-full max-w-[500px] flex gap-3">
        <template v-for="(image, idx) in images" :key="idx">
            <div class="shrink-0 max-w-[300px] relative">
                <button type="button"
                        class="absolute right-3 top-1 bg-gray-100 rounded-full p-1 hover:bg-gray-200"
                        @click="deleteImage(idx)"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2">
                        <path d="M18 6l-12 12"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
                <img :src="image" alt="" class="rounded-lg overflow-hidden">
            </div>
        </template>
    </div>
</template>
