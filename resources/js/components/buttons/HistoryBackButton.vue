<script setup>
const historyBack = () => {
    if (window.history.length > 1) {
         window.history.back();
    } else {
         location.href = '/';
    }
}
</script>
<template>
    <button type="button" @click="historyBack">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" stroke-width="2"> <path d="M5 12l14 0"></path> <path d="M5 12l4 4"></path> <path d="M5 12l4 -4"></path></svg>
    </button>
</template>
