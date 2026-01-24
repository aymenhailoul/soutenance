@props(['label', 'route', 'active' => false])

<a 
    href="{{ $route }}" 
    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ $active ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}"
    title="{{ $label }}"
>
    <div class="flex-shrink-0">
        {{ $slot }}
    </div>
    <span class="font-medium text-base whitespace-nowrap overflow-hidden sidebar-label">{{ $label }}</span>
</a>