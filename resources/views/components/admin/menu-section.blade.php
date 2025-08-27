@props([
    'title' => '',
    'class' => ''
])

<div class="px-6 py-3 {{ $class }}">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $title }}</p>
</div>