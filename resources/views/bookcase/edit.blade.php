@prepend('scripts')
    <script>
        window.__bookcase = @json($bookcase);
        window.__profileUser = @json($user);
    </script>
@endprepend
<x-app-layout>
    <suspense>
        <bookcase-form-component></bookcase-form-component>
    </suspense>
</x-app-layout>
