<template>
    <div class="dropdown relative">
        <div @click="toggleDropdown()">
            <slot name="mybutton"></slot>
        </div>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" v-if="show">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "DropdownComponent",
    data() {
        return {
            show: false,
        };
    },
    methods: {
        toggleDropdown() {
            console.log('111');
            this.show = !this.show;
        },
        handleClickOutside(event) {
            // 드롭다운 내부가 아닌 다른 영역 클릭 시 닫기
            const dropdown = this.$el; // 현재 컴포넌트의 루트 엘리먼트
            if (!dropdown.contains(event.target)) {
                this.show = false;
            }
        },
    },
    mounted() {
        // 컴포넌트가 마운트되면 이벤트 리스너 추가
        document.addEventListener("click", this.handleClickOutside);
    },
    beforeUnmount() {
        // 컴포넌트가 언마운트되기 전에 이벤트 리스너 제거
        document.removeEventListener("click", this.handleClickOutside);
    },
};
</script>
