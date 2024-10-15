<style>
    .modal{
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 100;
    }

    .modal .modal-content{
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 400px;
        height: 400px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }
</style>
@vite(['resources/js/pages/home.js']);
<x-app-layout>
    <x-navigation></x-navigation>
    <div class="container mx-auto max-w-lg w-full divide-y" x-data="feedData">
        <template x-for="feed in feeds.data" :key="feed.id">
            <div class="flex py-6" x-data="{open: false}">
                <div class="profile shrink-0 mr-3">
                    <img class="w-8 h-8 rounded-full" :src="feed.user.profile_photo_url" alt="Neil image">
                </div>
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="flex">
                            <div class="mr-3 font-bold" x-text="feed.user.name">아이디</div>
                            <div class="opacity-75" x-text="feed.created_at_human"></div>
                        </div>
                        <div class="relative">
                            <button type="button" @click.prevent="open = !open">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-dots" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                    <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                    <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                </svg>
                            </button>
                            <ul x-show="open"
                                class="dropdown w-48 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg absolute right-0 top-5 z-10"
                                @click.away="open = false">
                                <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200 rounded-t-lg"><a href="">저장</a></li>
                                <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">관심 없음</a></li>
                                <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">차단하기</a></li>
                                <li class="w-full px-4 py-2 hover:bg-indigo-50 border-b border-gray-200"><a href="">신고하기</a></li>
                                <li class="w-full px-4 py-2 hover:bg-indigo-50 rounded-b-lg"><a href=""><a href="">링크복사</a></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="mb-1" x-text="feed.note"></div>
                        <template x-if="feed.images.length > 0">
                            <div class="swiper border rounded-lg !w-full">
                                <div class="swiper-wrapper !w-full">
                                    <template x-for="image in feed.images" :key="image.id">
                                        <div class="swiper-slide !h-[300px] flex align-items-center justify-center bg-white !w-full">
                                            <img :src="image.image_url"
                                                 class="w-auto h-full block mx-auto"
                                                 alt="">
                                        </div>
                                    </template>
                                </div>
                                <!-- If we need pagination -->
                                <div class="swiper-pagination"></div>
                                <!-- If we need navigation buttons -->
    {{--                            <div class="swiper-button-prev"></div>--}}
    {{--                            <div class="swiper-button-next"></div>--}}
                            </div>
                        </template>
                        <div class="mt-3 flex gap-3">
                            <button type="button" @click="toggleLike(feed)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                </svg>
                            </button>
                            <button type="button" @click="showContent(feed)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 20l1.3 -3.9a9 8 0 1 1 3.4 2.9l-4.7 1" />
                                </svg>
                            </button>
                            <button type="button" @click="copyUrl(feed)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 14l11 -11" />
                                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <div class="modal" x-show="contentModalOpen">
            <div class="modal-content" @click.away="contentModalOpen = false">
                <div class="modal-header"></div>
                <div class="modal-body">
                    <div>
                        <input type="text"
                               class="w-full border rounded-lg border-gray-400">
                        <button button="">게시</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function feedData(){
          return {
            feeds: {
              current_page: 1,
              data: [],
              last_page: null,
              total: null,
            },
            loading: false,
            modalOpen: false,
            contentModalOpen: true,
            currentFeed: {
                id: null,
                user: {
                    name: null,
                    profile_photo_url: null,
                },
                note: null,
                images: [],
                comments: [],
            },
            async init(){
              let res = await this.getFeeds(this.feeds.current_page);
              this.feeds.data = [...this.feeds.data, ...res.data.data];
              this.feeds.current_page = res.data.current_page;
              this.feeds.last_page = res.data.last_page;
              window.addEventListener('scroll', this.onScroll.bind(this));
              this.$nextTick(i => {
                this.iniSwiper();
              });
            },
            iniSwiper(){
              document.querySelectorAll('.swiper').forEach(el => {
                const swiper = new Swiper(el, {
                  loop: true,
                  modules: [Navigation, Pagination],
                  autoHeight: true,
                  pagination: {
                    el: '.swiper-pagination',
                  },
                  // navigation: {
                  //   nextEl: '.swiper-button-next',
                  //   prevEl: '.swiper-button-prev',
                  // },
                });
              });
            },
            onScroll() {
              const scrollHeight = document.documentElement.scrollHeight;
              const scrollTop = document.documentElement.scrollTop;
              const clientHeight = document.documentElement.clientHeight;

              if (scrollTop + clientHeight >= scrollHeight * 0.8) {
                if (!this.loading && this.feeds.current_page < this.feeds.last_page) {
                  this.getFeeds(this.feeds.current_page + 1);
                }
              }
            },
            async getFeeds(page) {
              if (this.loading) return;
              this.loading = true;
              try{

                let params = {
                  page: page,
                }

                const res = await axios.get('/api/feeds', {
                  params: params
                })

                if (!res.data.status) {
                  throw new Error(res.data.data);
                }

                return res.data;

              } catch(Error) {
                  this.loading = false;
                alert(error.message);
              }
            },
            toggleModal(){
              this.modalOpen = !this.modalOpen;
            },
            toggleLike(feed){
              console.log(feed);
            },
            showContent(feed){
              console.log('qwe');
              this.contentModalOpen = true;
              console.log(this.contentModalOpen);
            },
            copyUrl(feed){
              // 변수 url를 클립보드에 복사하는 기능 textarea를 생성하여 복사하는 방법
                const url = window.location.origin + '/feeds/' + feed.id;
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                alert('URL이 복사되었습니다.');
            }
          }
        }
    </script>
    <x-footer></x-footer>
</x-app-layout>
