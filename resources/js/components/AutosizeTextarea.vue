<template>
    <div>
        <textarea
            id="autosize-textarea"
            v-model="message"
            ref="textareaRef"
            placeholder="새로운 감상이 있나요?"
        ></textarea>
    </div>
</template>

<script>
import { useTextareaAutosize } from '@vueuse/core'
const {textareaRef, message} = useTextareaAutosize();

export default {
    name: 'AutosizeTextarea',
    data() {
        return {
            message,
            textareaRef,
        }
    },
}
</script>

<style scoped>
textarea {
    width: 100%;
    min-height: 40px;
    resize: none;
    padding: 8px;
    font-size: 16px;
}
</style>
