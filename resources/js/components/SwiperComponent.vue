<template>
    <div class="swiper border rounded-lg">
        <div class="swiper-wrapper">
            <div class="swiper-slide !h-[300px] flex align-items-center justify-center bg-white"
                 v-for="image in images" :key="image.id">
                <img :src="image.image_url"
                     class="w-auto h-full block mx-auto"
                     alt="">
            </div>
        </div>

<!--        <slot name="pagination"></slot>-->
<!--        <slot name="navigationButton"></slot>-->
        <div class="swiper-pagination"></div>
<!--        <div class="swiper-button-prev"></div>-->
<!--        <div class="swiper-button-next"></div>-->
    </div>
</template>
<script>
import Swiper from "swiper";
import { Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
    name: "SwiperComponent",
    props: {
        images: {
            type: Array,
            required: true,
        },
        options: {
            type: Object,
            default: () => ({
                loop: false,
                modules: [Navigation, Pagination],
                autoHeight: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                // navigation: {
                //     nextEl: '.swiper-button-next',
                //     prevEl: '.swiper-button-prev',
                // },
                observer: true, // DOM 변경을 감지하여 업데이트
                observeParents: true, // 부모 DOM 변경을 감지
                on: {
                    slideChange: function (event) {
                        // Swiper 인스턴스의 컨텍스트에서 this가 Swiper 인스턴스를 가리키도록 함
                        const swiper = this; // this는 Swiper 인스턴스를 가리킴
                        const activeIndex = swiper.activeIndex; // 현재 활성화된 슬라이드의 인덱스
                        const paginationBullets = swiper.pagination.el.children; // 페이지네이션 엘리먼트 자식들

                        Array.from(paginationBullets).forEach((el, index) => {
                            if (index === activeIndex) {
                                el.classList.add('swiper-pagination-bullet-active');
                            } else {
                                el.classList.remove('swiper-pagination-bullet-active');
                            }
                        });
                    },
                },
            }),
        },
    },
    mounted() {
        let options = {...this.options};
        this.$nextTick(i => {
            const swiper = new Swiper('.swiper', options);
        });
    },
};
</script>
