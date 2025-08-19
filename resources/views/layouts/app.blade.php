<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name') . ' - 독서 기록과 소통의 플랫폼')</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('description', '북로그에서 독서 기록을 남기고, 책 리뷰를 공유하며, 독서 커뮤니티와 소통해보세요. AI가 추천하는 맞춤형 도서와 함께 더 풍부한 독서 경험을 만들어보세요.')">
    <meta name="keywords" content="@yield('keywords', '독서, 책리뷰, 독서기록, 북로그, 도서추천, 독서커뮤니티, 책추천, 서재, 독서일기')">
    <meta name="author" content="@yield('author', 'BookLog')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', request()->url())">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', '북로그에서 독서 기록을 남기고, 책 리뷰를 공유하며, 독서 커뮤니티와 소통해보세요.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="ko_KR">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', '북로그에서 독서 기록을 남기고, 책 리뷰를 공유하며, 독서 커뮤니티와 소통해보세요.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">

    <!-- Additional SEO Tags -->
    <meta name="theme-color" content="#1f2937">
    <meta name="msapplication-TileColor" content="#1f2937">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    @stack('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        .slide-fade-enter-active {
            transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .slide-fade-leave-active {
            transition: all 0.6s cubic-bezier(0.7, 0, 0.3, 1);
        }

        .slide-fade-enter-from,
        .slide-fade-leave-to {
            transform: translateY(-20px);
            opacity: 0;
        }

        .slide-fade-enter-to,
        .slide-fade-leave-from {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    @stack('scripts')

    <script>
        window.auth = @json(auth()->user());
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
</head>
<body id="app" class="font-sans antialiased">
<div class="min-h-screen bg-gray-50 md:ps-[82px] flex flex-col">

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer></x-footer>
</div>

<suspense>
    <comment-modal-component></comment-modal-component>
</suspense>

@stack('modals')
</body>
</html>
