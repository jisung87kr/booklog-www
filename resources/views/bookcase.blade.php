@prepend('scripts')
    <script>
        window.__bookcase = @json($bookcase);
    </script>
@endprepend
<x-app-layout>
    <suspense>
        <bookcase-component></bookcase-component>
    </suspense>
    <x-footer></x-footer>
</x-app-layout>
