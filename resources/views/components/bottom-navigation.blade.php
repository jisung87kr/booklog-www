<div x-data="navigationData">
    <div class="block md:hidden fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200">
        <div class="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="30" height="30" viewBox="0 0 24 24" stroke-width="2.3" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
            </a>
            <a href="{{ route('search.index') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="30" height="30" viewBox="0 0 24 24" stroke-width="2.3" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
            </a>
            <a href="" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group"
               @click.prevent="openCreateProcessForm">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="30" height="30" viewBox="0 0 24 24" stroke-width="2.3" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                    <path d="M16 5l3 3" />
                </svg>
            </a>
            <a href="#" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" width="30" height="30" viewBox="0 0 24 24" stroke-width="2.3" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                </svg>
            </a>
            <a href="{{ route('account.index') }}" class="inline-flex flex-col items-center justify-center px-5 hover:bg-gray-50 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="30" height="30" viewBox="0 0 24 24" stroke-width="2.3" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                </svg>
            </a>
        </div>
    </div>
    <div
            id="processFormModal"
            tabindex="-2"
            aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0"
    >
        <div class="relative max-h-full w-full max-w-2xl">
            <!-- Modal content -->
            <div class="relative rounded-lg bg-white shadow">
                <!-- Modal header -->
                <div
                        class="flex items-start justify-between rounded-t border-b p-5"
                >
                    <h3
                            class="text-xl font-semibold text-gray-900 dark:text-white lg:text-2xl"
                    >
                        새 게시물 만들기
                    </h3>
                    <button
                            type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900"
                            @click="closeProcessFormModal()"
                    >
                        <svg
                                class="h-3 w-3"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 14 14"
                        >
                            <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="space-y-6 p-6">
                    <div>
                        <div class="profile"></div>
                        <div class="my-2 border rounded-lg px-2 py-3 border-gray-400">
                            <button type="button" class="flex justify-between w-full" @click="addBook">
                                <span x-text="selectedBook.title ? selectedBook.title : '책추가'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book-2 shrink-0" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12z" />
                                    <path d="M19 16h-12a2 2 0 0 0 -2 2" />
                                    <path d="M9 8h6" />
                                </svg>
                            </button>
                        </div>
                        <div class="my-2">
                            <textarea name="" id="" cols="30" rows="10" class="w-full border rounded-lg border-gray-400"></textarea>
                            <div class="text-right">0/2000</div>
                        </div>
                        <div class="my-2 flex gap-3 items-center">
                            <input type="text" placeholder="시작페이지" class="w-full border rounded-lg border-gray-400">
                            <span>~</span>
                            <input type="text" placeholder="종료페이지" class="w-full border rounded-lg border-gray-400">
                        </div>
                        <div class="my-2">
                            <input type="text" placeholder="태그추가" class="w-full border rounded-lg border-gray-400">
                        </div>
                        <div class="my-2 border rounded-lg px-2 py-3 border-gray-400">
                            <div>
                                <input type="file" name="images[]" class="w-full border rounded-lg border-gray-400 hidden" x-ref="input_image" multiple>
                                <button type="button" class="flex justify-between w-full"  @click="addImages">
                                    <span>이미지 추가</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M15 8h.01" />
                                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                        class="flex items-center space-x-2 rtl:space-x-reverse rounded-b border-t border-gray-200 p-6"
                >
                    <button
                            type="button"
                            class="rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        공유하기
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div
            id="drawer-js-example"
            class="fixed z-[60] h-screen w-80 overflow-y-auto bg-white p-4 transition-transform right-0 top-0 translate-x-full"
            tabindex="-1"
            aria-labelledby="drawer-js-label"
    >
        <h5
                id="drawer-js-label"
                class="mb-4 inline-flex items-center text-base font-semibold text-gray-500"
        ></h5>
        <button
                id="drawer-hide-button"
                type="button"
                aria-controls="drawer-example"
                class="absolute right-2.5 top-2.5 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900"
        >
            <svg
                    class="h-3 w-3"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 14 14"
            >
                <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                />
            </svg>
            <span class="sr-only">Close menu</span>
        </button>
        <div>
            <ul class="max-w-md divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="book in data.data">
                    <li class="py-3 sm:pb-4">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="w-8 h-10 rounded-lg" src="https://placehold.co/200x300">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="book.title"></p>
                                <p class="text-sm text-gray-500 truncate dark:text-gray-400" x-text="book.author"></p>
                            </div>
                            <div class="inline-flex items-center text-base font-semibold text-gray-900">
                                <button type="button" @click="selectBook(book)">선택</button>
                            </div>
                        </div>
                    </li>
                </template>
            </ul>
            <div class="flex">
                <button type="button"
                   class="flex items-center justify-center px-3 h-8 me-3 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700"
                   @click="previousPage"
                   :disabled="data.current_page == 1">
                    <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5H1m0 0 4 4M1 5l4-4"/>
                    </svg>
                    이전
                </button>
                <button type="button"
                   class="flex items-center justify-center px-3 h-8 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700"
                    @click="nextPage"
                    :disabled="data.current_page == data.last_page">
                    다음
                    <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                    </svg>
                </button>
            </div>
            <div class="mt-3">
                <a
                        href="#"
                        class="inline-flex items-center rounded-lg bg-blue-700 px-4 py-2 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300"
                >책장관리
                    <svg
                            class="ms-2 h-3.5 w-3.5"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 10"
                    >
                        <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M1 5h12m0 0L9 1m4 4L9 9"
                        /></svg
                    ></a>
            </div>
        </div>
    </div>
</div>
<script>
  function navigationData(){
    return {
      user: @json(auth()->user()),
      processFormModal: null,
      drawer: null,
      data:{
        current_page: 1,
        data: [],
        links: [],
        per_page: null,
        total: null,
        last_page: null,
      },
      selectedBook: {
        author: '',
        title: '',
        description: '',
        isbn: '',
        cover_image: '',
        total_pages: '',
        publisher: '',
        published_date: '',
      },
      content: '',
      startPage: null,
      endPage: null,
      images: [],
      tags: [],
      init(){
        this.initCreateProcessFormModal();
        this.initDrawers();
        this.getBooks();
        setTimeout(() => {
          // this.processFormModal.hide();
        }, 10000);
      },
      initCreateProcessFormModal(){
        const processFormModalEl = document.getElementById('processFormModal');
        const options = {
          placement: 'center',
          backdrop: 'dynamic',
          backdropClasses:
            'bg-gray-900/50 fixed inset-0 z-40',
          closable: true,
          onHide: () => {
            console.log('modal is hidden');
          },
          onShow: () => {
            console.log('modal is shown');
          },
          onToggle: () => {
            console.log('modal has been toggled');
          },
        };

        const instanceOptions = {
          id: 'processFormModal',
          override: true
        };

        this.processFormModal = new Modal(processFormModalEl, options, instanceOptions);
      },
      initDrawers(){
        // set the drawer menu element
        const $targetEl = document.getElementById('drawer-js-example');

        const options = {
          placement: 'right',
          backdrop: true,
          bodyScrolling: false,
          edge: false,
          edgeOffset: '',
          backdropClasses:
            'bg-gray-900/50 fixed inset-0 z-50',
          onHide: () => {
            console.log('drawer is hidden');
          },
          onShow: () => {
            console.log('drawer is shown');
          },
          onToggle: () => {
            console.log('drawer has been toggled');
          },
        };

        const instanceOptions = {
          id: 'drawer-js-example',
          override: true
        };

        this.drawer = new Drawer($targetEl, options, instanceOptions);
      },
      openCreateProcessForm(){
        if(!this.user){
          alert('로그인 후 이용해주세요');
          window.location.href = "/login";
          return false;
        }
        this.processFormModal.show();
      },
      closeProcessFormModal(){
        this.processFormModal.hide();
      },
      addBook(){
        this.drawer.show();
      },
      selectBook(book){
        this.selectedBook = book;
        this.drawer.hide();
      },
      getBooks(){
        let params = {
          page: this.data.current_page,
        }
        axios.get(`/api/users/${this.user.id}/books`, {
          params: params,
        }).then(res => {
          console.log(res.data);
          if(!res.data.status){
            throw new Error(res.data.message);
          }
          this.data = res.data.data;
        }).catch(error => {
          alert(error.message);
        });
      },
      addImages(){
        this.$refs.input_image.click();
      },
      previousPage(){
        if(this.data.current_page == 1){
          return false;
        }
        this.data.current_page = this.data.current_page - 1;
        this.getBooks();
      },
      nextPage(){
        if(this.data.current_page >= this.data.last_page){
          return false;
        }
        this.data.current_page = this.data.current_page + 1;
        this.getBooks();
      }
    }
  }
</script>
