<template>
    <div v-if="isVisible" class="modal-wrapper" @click.self="closeModal">
        <div class="modal" :style="{ width: computedWidth }">
            <slot name="modal-header"></slot>
            <div class="modal-body">
                <slot></slot>
            </div>
            <slot name="modal-footer"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isVisible: {
            type: Boolean,
            default: false,
        },
        width: {
            type: String,
            default: "500px", // 기본값을 지정
        },
    },
    computed: {
        computedWidth() {
            // 유효한 CSS 단위가 포함되지 않으면 px로 처리
            return this.width.match(/^\d+$/) ? `${this.width}px` : this.width;
        },
    },
    methods: {
        closeModal() {
            this.$emit("close");
        },
    },
};
</script>

<style scoped>
.modal-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
    padding: 20px;
}

.modal {
    background: white;
    border-radius: 8px;
    max-width: 100%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.modal-body {
    max-height: 80%;
    //overflow: auto;
}

button {
    padding: 8px 12px;
    background-color: #007bff;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 4px;
}

button:hover {
    background-color: #0056b3;
}
</style>
