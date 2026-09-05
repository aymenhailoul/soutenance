@extends('layouts.app')

@section('title', 'Tableau de Bord IT')

@section('content')
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Système de Gestion du Parc Informatique & Infrastructure</h1>
                <p class="text-blue-100 text-sm mt-1">ISS Maroc — Suivi des Équipements, Sites, Affectations & Maintenances
                </p>
            </div>
            <div class="hidden md:block text-right">
                <span class="text-xs bg-white/20 px-3 py-1.5 rounded-full font-medium backdrop-blur-sm">
                    {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Equipment -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Total
                        Équipements</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $totalEquipment }}</p>
                </div>
            </div>

            <!-- Available Equipment -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Disponibles en
                        Stock</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $availableEquipment }}</p>
                </div>
            </div>

            <!-- Assigned Equipment -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Affectés sur
                        Sites</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $assignedEquipment }}</p>
                </div>
            </div>

            <!-- Maintenance Equipment -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">En Maintenance
                    </p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $inMaintenanceEquipment }}</p>
                </div>
            </div>
        </div>

        <!-- Infrastructure Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium">Catégories
                        d'Équipement</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $totalCategories }}</h3>
                </div>
                <a href="{{ route('categories.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Gérer &rarr;</a>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium">Clients Entreprises</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $totalClients }}</h3>
                </div>
                <a href="{{ route('clients.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Gérer &rarr;</a>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
                <div>
                    <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-medium">Sites Clients</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $totalSites }}</h3>
                </div>
                <a href="{{ route('sites.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Gérer &rarr;</a>
            </div>
        </div>

        <!-- Alerts Section: Low Stock & Expiring Warranties -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Low Stock Warnings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full inline-block"></span>
                        Alertes Stock Bas Consommables
                    </h3>
                    <a href="{{ route('stock.index') }}"
                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Mouvements
                        &rarr;</a>
                </div>
                @if($lowStockConsumables->count() > 0)
                    <div class="space-y-3">
                        @foreach($lowStockConsumables as $item)
                            <div
                                class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->name }}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Stock Min: {{ $item->min_stock }}</span>
                                </div>
                                <span class="px-3 py-1 bg-red-600 text-white rounded-full text-xs font-bold">
                                    {{ $item->quantity }} en stock
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 py-4 text-center">
                        Aucune alerte de stock bas sur les consommables.
                    </p>
                @endif
            </div>

            <!-- Warranty Expiration Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                        <span class="w-3 h-3 bg-amber-500 rounded-full inline-block"></span>
                        Garanties Expirant sous 30 jours
                    </h3>
                    <a href="{{ route('equipment.index') }}"
                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Parc IT &rarr;</a>
                </div>
                @if($expiringWarranties->count() > 0)
                    <div class="space-y-3">
                        @foreach($expiringWarranties as $eq)
                            <div
                                class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $eq->name }}</h4>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">S/N:
                                        {{ $eq->serial_number ?? 'N/A' }}</span>
                                </div>
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400">
                                    {{ $eq->warranty_end_date ? $eq->warranty_end_date->format('d/m/Y') : '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 py-4 text-center">
                        Aucune garantie n'expire dans les 30 prochains jours.
                    </p>
                @endif
            </div>

        </div>

        <!-- Recent Activities Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Assignments -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4">Dernières Affectations</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        <thead>
                            <tr>
                                <th class="text-left py-2 text-gray-500 font-medium">Équipement</th>
                                <th class="text-left py-2 text-gray-500 font-medium">Client / Site</th>
                                <th class="text-right py-2 text-gray-500 font-medium">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentAssignments as $asgn)
                                <tr>
                                    <td class="py-2.5 font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $asgn->equipment->name }}</td>
                                    <td class="py-2.5 text-gray-600 dark:text-gray-400">
                                        {{ $asgn->client->name ?? '-' }} ({{ $asgn->site->name ?? 'Général' }})
                                    </td>
                                    <td class="py-2.5 text-right text-gray-500">{{ $asgn->assigned_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-400">Aucune affectation récente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Maintenances -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4">Dernières Maintenances</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
                        <thead>
                            <tr>
                                <th class="text-left py-2 text-gray-500 font-medium">Titre</th>
                                <th class="text-left py-2 text-gray-500 font-medium">Équipement</th>
                                <th class="text-center py-2 text-gray-500 font-medium">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($recentMaintenances as $maint)
                                <tr>
                                    <td class="py-2.5 font-semibold text-gray-900 dark:text-gray-100">{{ $maint->title }}</td>
                                    <td class="py-2.5 text-gray-600 dark:text-gray-400">{{ $maint->equipment->name }}</td>
                                    <td class="py-2.5 text-center">
                                        <span class="px-2 py-0.5 rounded text-2xs font-bold
                                                    @if($maint->status === 'Completed') bg-green-100 text-green-800
                                                    @elseif($maint->status === 'In Progress') bg-amber-100 text-amber-800
                                                    @else bg-gray-100 text-gray-700 @endif">
                                            {{ $maint->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-400">Aucune maintenance récente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection