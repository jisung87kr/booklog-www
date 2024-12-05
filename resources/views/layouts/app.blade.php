<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <style>
            .slide-fade-enter-active {
                transition: all 0.3s ease-out;
            }

            .slide-fade-leave-active {
                transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
            }

            .slide-fade-enter-from,
            .slide-fade-leave-to {
                transform: translateY(-20px);
                opacity: 0;
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
        <div class="min-h-screen bg-gray-100 md:ps-[82px] flex flex-col">

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
