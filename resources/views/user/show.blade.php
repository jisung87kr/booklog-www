<x-app-layout>
    <div id="app" class="container-fluid mx-auto w-full sm:pt-3">
        <div class="flex justify-center mt-3 md:mt-0">
            <div class="bg-white shadow w-full rounded-2xl max-w-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between">
                        <div class="mr-3">
                            <div class="text-3xl font-bold">name</div>
                            <div>username</div>
                        </div>
                        <div>
                            <img src="https://placehold.co/300x300" alt="" class="w-20 h-20 rounded-full bg-red border">
                        </div>
                    </div>
                    <div class="my-3">description</div>
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-zinc-500 flex">
                            <div>팔로워 1,000명</div>
                            <span class="px-1">∙</span><a href="" target="_blank">https://naver.com</a>
                        </div>
                        <button type="button" class="rounded-full border border-zinc-900 w-6 h-6 text-center flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="1">
                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4 my-6">
                        <button type="button" class="rounded-lg px-3 py-2 bg-zinc-800 text-white text-sm font-bold">팔로우</button>
                        <button type="button" class="rounded-lg px-3 py-2 border border-zinc-300 text-sm font-bold">언급</button>
                    </div>
                </div>
                <div class="grid grid-cols-3">
                    <button type="button" class="text-sm px-3 py-2 font-bold text-zinc-900 border-b border-zinc-900">스레드</button>
                    <button type="button" class="text-sm px-3 py-2 font-bold text-zinc-500 border-b">답글</button>
                    <button type="button" class="text-sm px-3 py-2 font-bold text-zinc-500 border-b">리포스트</button>
                </div>
                <div class="p-6">

                </div>
            </div>
        </div>
    </div>
    <x-footer></x-footer>
</x-app-layout>
