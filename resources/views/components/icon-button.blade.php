@props([
    'variant' => 'secondary', // secondary|primary|danger|success
    'type' => 'button',
    'title' => null,
])

@php
    $base = 'inline-flex items-center justify-center w-9 h-9 rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed';
    $variants = [
        'primary' => 'text-blue-600 hover:bg-blue-50 focus:ring-blue-500 dark:text-blue-400 dark:hover:bg-blue-900/30',
        'secondary' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-400 dark:text-gray-300 dark:hover:bg-gray-700',
        'danger' => 'text-red-600 hover:bg-red-50 focus:ring-red-500 dark:text-red-400 dark:hover:bg-red-900/30',
        'success' => 'text-green-600 hover:bg-green-50 focus:ring-green-500 dark:text-green-400 dark:hover:bg-green-900/30',
    ];

    $className = trim($base.' '.($variants[$variant] ?? $variants['secondary']).' '.($attributes->get('class') ?? ''));
@endphp

<button type="{{ $type }}" @if($title) title="{{ $title }}" aria-label="{{ $title }}" @endif {{ $attributes->merge(['class' => $className]) }}>
    {{ $slot }}
</button>

