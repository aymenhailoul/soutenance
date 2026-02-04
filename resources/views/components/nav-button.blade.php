@props(['label', 'route', 'active' => false])

<a 
    href="{{ $route }}" 
    class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all {{ $active ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
    title="{{ $label }}"
>
    <div class="flex-shrink-0">
        {{ $slot }}
    </div>
    <span class="font-medium text-base whitespace-nowrap overflow-hidden sidebar-label">{{ $label }}</span>
</a>