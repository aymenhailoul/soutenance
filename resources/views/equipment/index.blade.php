@extends('layouts.app')

@section('title', 'Inventaire des Équipements - ISS Maroc')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Inventaire des Équipements</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gestion et suivi du parc informatique ISS Maroc</p>
        </div>
        <a href="{{ route('equipment.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Ajouter un Équipement
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-300 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('equipment.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, N° Série, Asset Tag, Marque..." class="w-full pl-10 pr-4 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category_id" class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    @foreach(['Available' => 'Disponible', 'Assigned' => 'Assigné', 'In Maintenance' => 'En Maintenance', 'Broken' => 'En Panne', 'Retired' => 'Retiré', 'Lost' => 'Perdu'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                    Filtrer
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'status', 'condition']))
                    <a href="{{ route('equipment.index') }}" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-900 dark:hover:bg-gray-800 text-gray-500 text-sm font-medium rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3">Équipement</th>
                        <th class="px-6 py-3">Catégorie</th>
                        <th class="px-6 py-3">N° Série / Tag</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3">État</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    @forelse($equipments as $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('equipment.show', $item) }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $item->name }}
                                </a>
                                @if($item->brand || $item->model)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->brand }} {{ $item->model }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                {{ $item->category->name ?? 'Non catégorisé' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-600 dark:text-gray-400 space-y-0.5">
                                @if($item->asset_tag)
                                    <span class="inline-block bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $item->asset_tag }}
                                    </span>
                                @endif
                                @if($item->serial_number)
                                    <div>SN: {{ $item->serial_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'Available' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                        'Assigned' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                                        'In Maintenance' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                        'Broken' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                        'Retired' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'Lost' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                    ];
                                    $statusLabels = [
                                        'Available' => 'Disponible',
                                        'Assigned' => 'Assigné',
                                        'In Maintenance' => 'En Maintenance',
                                        'Broken' => 'En Panne',
                                        'Retired' => 'Retiré',
                                        'Lost' => 'Perdu',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-700 dark:text-gray-300">
                                {{ $item->condition }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('equipment.show', $item) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white font-medium">
                                    Voir
                                </a>
                                <a href="{{ route('equipment.edit', $item) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Éditer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Aucun équipement trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($equipments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $equipments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
