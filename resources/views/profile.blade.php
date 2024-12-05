@prepend('scripts')
<script>
    window.__profileUser = @json($user);
</script>
@endprepend
<x-app-layout>
    <suspense>
        <profile-component></profile-component>
    </suspense>
    <x-footer></x-footer>
</x-app-layout>
