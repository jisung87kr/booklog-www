@vite(['resources/js/pages/home.js']);
<x-app-layout>
    <x-navigation></x-navigation>
    <div id="app" class="container mx-auto max-w-lg w-full divide-y">
        <feed-component :feed="feed"
                        v-for="feed in feeds.data"
                        :key="feed.id"
                        class="py-6"></feed-component>
    </div>
    <x-footer></x-footer>
</x-app-layout>
