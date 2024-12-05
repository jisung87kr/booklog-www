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
        @stack('scripts')
        <script>
            window.auth = @json(auth()->user());
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
    </head>
    <body id="app" class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 md:ps-[82px]">

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <suspense>
            <comment-modal-component></comment-modal-component>
        </suspense>
        @stack('modals')
    </body>
</html>
