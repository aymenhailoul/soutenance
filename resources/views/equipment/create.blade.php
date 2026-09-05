@extends('layouts.app')

@section('title', 'Ajouter un Équipement - ISS Maroc')

@section('content')
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Ajouter un Équipement</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Enregistrer un nouvel équipement informatique ou
                    consommable</p>
            </div>
            <a href="{{ route('equipment.index') }}"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg transition-colors">
                Retour à l'inventaire
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

        <form method="POST" action="{{ route('equipment.store') }}"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-6">
            @csrf

            <!-- General Info Section -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Informations Générales</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom de l'équipement
                            *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="ex: Dell PowerEdge R740"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                        <select name="category_id"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Marque</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="ex: Dell, Cisco, HP"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modèle</label>
                        <input type="text" name="model" value="{{ old('model') }}" placeholder="ex: PowerEdge R740"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de
                            Série</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                            placeholder="ex: SN-987654321"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Asset Tag (Tag
                            d'Inventaire)</label>
                        <input type="text" name="asset_tag" value="{{ old('asset_tag') }}" placeholder="ex: ISS-SRV-001"
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
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prix d'achat
                            (MAD)</label>
                        <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}"
                            placeholder="0.00"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fin de
                            garantie</label>
                        <input type="date" name="warranty_end_date" value="{{ old('warranty_end_date') }}"
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut *</label>
                        <select name="status" required
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="Available" {{ old('status', 'Available') == 'Available' ? 'selected' : '' }}>
                                Disponible</option>
                            <option value="Assigned" {{ old('status') == 'Assigned' ? 'selected' : '' }}>Assigné</option>
                            <option value="In Maintenance" {{ old('status') == 'In Maintenance' ? 'selected' : '' }}>En
                                Maintenance</option>
                            <option value="Broken" {{ old('status') == 'Broken' ? 'selected' : '' }}>En Panne</option>
                            <option value="Retired" {{ old('status') == 'Retired' ? 'selected' : '' }}>Retiré</option>
                            <option value="Lost" {{ old('status') == 'Lost' ? 'selected' : '' }}>Perdu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition *</label>
                        <select name="condition" required
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="New" {{ old('condition', 'Good') == 'New' ? 'selected' : '' }}>Neuf</option>
                            <option value="Good" {{ old('condition', 'Good') == 'Good' ? 'selected' : '' }}>Bon état</option>
                            <option value="Fair" {{ old('condition') == 'Fair' ? 'selected' : '' }}>Moyen</option>
                            <option value="Damaged" {{ old('condition') == 'Damaged' ? 'selected' : '' }}>Endommagé</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Consumable & Stock -->
            <div class="space-y-4">
                <h3
                    class="text-base font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                    Gestion du Stock & Consommable</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div class="sm:col-span-1 pt-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_consumable" value="1" {{ old('is_consumable') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Est un consommable (Câble,
                                souris, etc.)</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantité *</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Seuil Alerte Stock
                            Bas</label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                            class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes / Remarques</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                    placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('equipment.index') }}"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg">
                    Annuler
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm">
                    Enregistrer l'Équipement
                </button>
            </div>
        </form>
    </div>
@endsection