<script>
    window.userData = @json($user);
</script>
<x-app-layout>
    <suspense>
        <profile-component></profile-component>
    </suspense>
    <x-footer></x-footer>
</x-app-layout>
