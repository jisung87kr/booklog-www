<nav {{ $attributes->merge(['class' => 'bg-white border-b border-gray-200']) }}x-data="">
    <div class="max-w-2xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap">{{ config('app.name') }}</span>
        </a>
        <button data-collapse-toggle="navbar-default" type="button"
                class="hidden md:inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>
        <div class="">
            @if(auth()->check())
                <a href="{{ route('logout') }}" class="rounded-lg bg-indigo-600 text-white px-3 py-2 text-sm" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">로그아웃</a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                    @csrf
                    @method('POST')
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 text-white px-3 py-2 text-sm">로그인</a>
            @endif
        </div>
    </div>
</nav>
