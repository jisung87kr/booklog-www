<div>
    <div class="fixed bottom-0 left-0 z-50 w-full h-16 bg-white border-t border-gray-200 flex md:top-0 md:h-full md:w-auto md:items-center md:border-r place-content-between md:flex-col items-center">
        <div class="hidden md:block p-5 font-extrabold">
            <a href="{{ route('home') }}">북로그</a>
        </div>
        <div class="flex max-w-2xl mx-auto font-medium md:block items-center align-items-center">
            <a href="{{ route('home') }}" class="inline-flex flex-row items-center justify-center px-5 hover:bg-gray-50 group md:block p-5 rounded-2xl {{ request()->routeIs('home') ? 'bg-gray-100' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>
            </a>
            <a href="{{ route('search.index') }}" class="inline-flex flex-row items-center justify-center px-5 hover:bg-gray-50 group md:block p-5 rounded-2xl {{ request()->routeIs('search.index') ? 'bg-gray-100' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                    <path d="M21 21l-6 -6" />
                </svg>
            </a>
            <post-form-component>
                <button type="button" class="inline-flex flex-row items-center justify-center px-5 hover:bg-gray-50 group md:block p-5 rounded-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                        <path d="M16 5l3 3" />
                    </svg>
                </button>
            </post-form-component>
            <a href="{{route('activity.index')}}" class="inline-flex flex-row items-center justify-center px-5 hover:bg-gray-50 group md:block p-5 rounded-2xl {{ request()->routeIs('activity.index') ? 'bg-gray-100' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heart" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                </svg>
            </a>
            <a href="{{ Auth::user() ? route('profile', ['user' => Auth::user()->username]) : route('login') }}" class="inline-flex flex-row items-center justify-center px-5 hover:bg-gray-50 group md:block p-5 rounded-2xl {{ request()->routeIs('profile') ? 'bg-gray-100' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="30" height="30" viewBox="0 0 24 24" stroke-width="2" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                </svg>
            </a>
        </div>
        <div class="h-10 md:mb-6">
            @if(Auth::user())
            <dropdown-component>
                <template v-slot:mybutton>
                    <button type="button" class="">
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="w-10 h-10 rounded-full bg-gray-100">
                    </button>
                </template>
                <div class="absolute right-0 md:left-[10px] top-[-90px] w-[120px] bg-white rounded-xl shadow">
                    <a href="{{ route('profile', ['user' => Auth::user()->username]) }}" class="block px-3 py-2 hover:bg-gray-100">프로필</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block px-3 py-2 hover:bg-gray-100 w-full text-left">로그아웃</button>
                    </form>
                </div>
            </dropdown-component>
            @endif
        </div>
    </div>
</div>
