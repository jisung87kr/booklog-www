@props([
    'icon' => null,
    'label' => '',
    'routePattern' => null
])

<div class="relative admin-dropdown" data-route-pattern="{{ $routePattern }}">
    <button class="admin-nav-link w-full text-left flex items-center justify-between {{ $routePattern && request()->routeIs($routePattern) ? 'active' : '' }}" onclick="toggleDropdown(this)">
        <div class="flex items-center">
            @if($icon)
                <i class="{{ $icon }} w-5 h-5"></i>
            @endif
            <span>{{ $label }}</span>
        </div>
        <i class="fas fa-chevron-down transform transition-transform duration-200 dropdown-chevron"></i>
    </button>
    
    <div class="dropdown-content pl-6 space-y-1 bg-gray-50 {{ $routePattern && request()->routeIs($routePattern) ? '' : 'hidden' }}">
        {{ $slot }}
    </div>
</div>