@props([
    'route' => null,
    'routePattern' => null,
    'icon' => null,
    'label' => '',
    'type' => 'link' // link, button, dropdown
])

@if($type === 'link')
    <a href="{{ $route ? route($route) : '#' }}" 
       class="admin-nav-link {{ $routePattern && request()->routeIs($routePattern) ? 'active' : '' }}">
        @if($icon)
            <i class="{{ $icon }} w-5 h-5"></i>
        @endif
        <span>{{ $label }}</span>
    </a>
@elseif($type === 'button')
    <button {{ $attributes->merge(['class' => 'admin-nav-button']) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <span>{{ $label }}</span>
    </button>
@endif