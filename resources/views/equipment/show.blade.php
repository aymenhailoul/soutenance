@extends('layouts.app')

@section('title', $equipment->name . ' - Détails Équipement')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $equipment->name }}</h1>
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
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$equipment->status] ?? 'bg-gray-100' }}">
                    {{ $statusLabels[$equipment->status] ?? $equipment->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Catégorie: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $equipment->category->name ?? 'Non spécifiée' }}</span> | 
                Asset Tag: <span class="font-mono text-gray-700 dark:text-gray-300">{{ $equipment->asset_tag ?? '—' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('equipment.edit', $equipment) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition-colors shadow-sm">
                Éditer l'Équipement
            </a>
            <a href="{{ route('equipment.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                Retour
            </a>
        </div>
    </div>

    <!-- Flash Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-300 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- General Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Informations Générales</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Marque:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->brand ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Modèle:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->model ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">N° de Série:</dt>
                    <dd class="font-mono text-gray-900 dark:text-white">{{ $equipment->serial_number ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Asset Tag:</dt>
                    <dd class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ $equipment->asset_tag ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Consommable:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->is_consumable ? 'Oui' : 'Non' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Quantité en stock:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->stock }}</dd>
                </div>
            </dl>
        </div>

        <!-- Procurement Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Achat & Garantie</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Date d'achat:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->purchase_date ? $equipment->purchase_date->format('d/m/Y') : '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Prix d'achat:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->purchase_price ? number_format($equipment->purchase_price, 2, ',', ' ') . ' MAD' : '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Fin de Garantie:</dt>
                    <dd class="font-medium {{ $equipment->warranty_end_date && $equipment->warranty_end_date->isPast() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-gray-900 dark:text-white' }}">
                        {{ $equipment->warranty_end_date ? $equipment->warranty_end_date->format('d/m/Y') : '—' }}
                        @if($equipment->warranty_end_date && $equipment->warranty_end_date->isPast())
                            (Expirée)
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Current State Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
            <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">État & Emplacement</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Condition physique:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->condition }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Site d'affectation:</dt>
                    <dd class="font-medium text-gray-900 dark:text-white">{{ $equipment->site->name ?? 'Non affecté à un site' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Notes Card -->
    @if($equipment->notes)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-2">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Notes / Observations</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $equipment->notes }}</p>
        </div>
    @endif

    <!-- Assignment History Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Historique des Affectations</h3>
        @if($equipment->assignments && $equipment->assignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-2">Employé / Site</th>
                            <th class="px-4 py-2">Date affectation</th>
                            <th class="px-4 py-2">Date retour</th>
                            <th class="px-4 py-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->assignments as $assignment)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $assignment->employee->name ?? $assignment->site->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $assignment->assigned_at ? $assignment->assigned_at->format('d/m/Y H:i') : '—' }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $assignment->returned_at ? $assignment->returned_at->format('d/m/Y H:i') : 'En cours' }}</td>
                                <td class="px-4 py-2">{{ $assignment->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 py-2 text-center">Aucune affectation enregistrée.</p>
        @endif
    </div>

    <!-- Maintenance History Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Historique de Maintenance</h3>
        @if($equipment->maintenances && $equipment->maintenances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2">Période</th>
                            <th class="px-4 py-2">Coût</th>
                            <th class="px-4 py-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->maintenances as $maint)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $maint->type }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $maint->description }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $maint->start_date }} - {{ $maint->end_date ?? 'En cours' }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $maint->cost ? number_format($maint->cost, 2) . ' MAD' : '—' }}</td>
                                <td class="px-4 py-2">{{ $maint->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 py-2 text-center">Aucune intervention de maintenance enregistrée.</p>
        @endif
    </div>

    <!-- Stock Movements Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <h3 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Mouvements de Stock</h3>
        @if($equipment->stockMovements && $equipment->stockMovements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 text-xs font-semibold text-gray-500 uppercase">
                            <th class="px-4 py-2">Mouvement</th>
                            <th class="px-4 py-2">Quantité</th>
                            <th class="px-4 py-2">Utilisateur</th>
                            <th class="px-4 py-2">Commentaire</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($equipment->stockMovements as $mov)
                            <tr>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $mov->movement == 'Entrée' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $mov->movement }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-bold text-gray-900 dark:text-white">{{ $mov->quantity }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $mov->user->name ?? 'Système' }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $mov->comment ?? '—' }}</td>
                                <td class="px-4 py-2 text-gray-500 text-xs">{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 py-2 text-center">Aucun mouvement de stock enregistré.</p>
        @endif
    </div>
</div>
@endsection
