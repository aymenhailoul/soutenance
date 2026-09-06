@extends('layouts.app')

@section('title', 'Modifier Équipement - ISS Maroc')

@section('content')
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modifier l'Équipement</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $equipment->name }}
                    ({{ $equipment->asset_tag ?? $equipment->serial_number ?? 'ID: #' . $equipment->id }})</p>
            </div>
            <a href="{{ route('equipment.show', $equipment) }}"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                Voir les détails
            </a>
        </div>

        @if ($errors->any())
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-900/30 dark:border-rose-800 dark:text-rose-300 rounded-lg space-y-1">
                <div class="font-semibold text-sm">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('equipment.update', $equipment) }}"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- General Info Section -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Informations Générales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de l'équipement
                            *</label>
                        <input type="text" name="name" value="{{ old('name', $equipment->name) }}" required
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                        <select name="category_id"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $equipment->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Marque</label>
                        <input type="text" name="brand" value="{{ old('brand', $equipment->brand) }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modèle</label>
                        <input type="text" name="model" value="{{ old('model', $equipment->model) }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de
                            Série</label>
                        <input type="text" name="serial_number"
                            value="{{ old('serial_number', $equipment->serial_number) }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asset Tag</label>
                        <input type="text" name="asset_tag" value="{{ old('asset_tag', $equipment->asset_tag) }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Procurement & Warranty -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Achat & Garantie</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'achat</label>
                        <input type="date" name="purchase_date"
                            value="{{ old('purchase_date', $equipment->purchase_date ? $equipment->purchase_date->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix d'achat
                            (MAD)</label>
                        <input type="number" step="0.01" name="purchase_price"
                            value="{{ old('purchase_price', $equipment->purchase_price) }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fin de
                            garantie</label>
                        <input type="date" name="warranty_end_date"
                            value="{{ old('warranty_end_date', $equipment->warranty_end_date ? $equipment->warranty_end_date->format('Y-m-d') : '') }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Status & State -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Statut & État</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut du Cycle de Vie</label>
                        @if(in_array($equipment->status, ['Assigned', 'In Maintenance']))
                            <input type="hidden" name="status" value="{{ $equipment->status }}">
                            <div class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                <span>{{ $equipment->status === 'Assigned' ? 'Assigné (En cours)' : 'En Maintenance' }}</span>
                                <span class="text-xs font-normal text-gray-500">(Verrouillé par le système)</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Le statut est géré par les modules d'affectation et de maintenance.</p>
                        @else
                            <select name="status" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                @foreach(['Available' => 'Disponible', 'Broken' => 'En Panne', 'Retired' => 'Retiré', 'Lost' => 'Perdu'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('status', $equipment->status) == $val ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition *</label>
                        <select name="condition" required
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @foreach(['New' => 'Neuf', 'Good' => 'Bon état', 'Fair' => 'Moyen', 'Damaged' => 'Endommagé'] as $val => $label)
                                <option value="{{ $val }}" {{ old('condition', $equipment->condition) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Consumable & Stock -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Gestion du Stock & Consommable</h3>
                @php
                    $hasHistory = $equipment->assignments()->exists() || $equipment->maintenances()->exists() || $equipment->stockMovements()->exists();
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div class="sm:col-span-1 pt-4">
                        <label class="inline-flex items-center gap-2 {{ $hasHistory ? 'cursor-not-allowed opacity-75' : 'cursor-pointer' }}">
                            <input type="checkbox" name="is_consumable" value="1" {{ old('is_consumable', $equipment->is_consumable) ? 'checked' : '' }}
                                {{ $hasHistory ? 'disabled' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            @if($hasHistory)
                                <input type="hidden" name="is_consumable" value="{{ $equipment->is_consumable ? '1' : '0' }}">
                            @endif
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Est un consommable</span>
                        </label>
                        @if($hasHistory)
                            <p class="text-xs text-gray-500 mt-1">Verrouillé (Historique d'opérations existant)</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantité Actuelle</label>
                        @if($equipment->is_consumable)
                            <div class="flex items-center gap-2">
                                <div class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm font-bold text-gray-900 dark:text-white flex-1">
                                    {{ $equipment->quantity }} unités
                                </div>
                                <a href="{{ route('stock.index') }}" class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-semibold rounded-lg transition-colors">
                                    Ajuster le stock
                                </a>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">La quantité s'ajuste uniquement via les Mouvements de Stock</p>
                        @else
                            <input type="number" value="1" readonly
                                class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Matériel individuel (Quantité = 1)</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seuil Alerte Stock Bas</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', $equipment->min_stock) }}" min="0"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes / Remarques</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">{{ old('notes', $equipment->notes) }}</textarea>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" form="delete-equipment-form-{{ $equipment->id }}"
                    class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 dark:text-rose-300 text-sm font-medium rounded-lg transition-colors">
                    Archiver / Supprimer
                </button>
                <div class="flex gap-3">
                    <a href="{{ route('equipment.show', $equipment) }}"
                        class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                        Annuler
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-equipment-form-{{ $equipment->id }}" method="POST" action="{{ route('equipment.destroy', $equipment) }}"
            onsubmit="return confirm('Êtes-vous sûr de vouloir archiver cet équipement ?')">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection