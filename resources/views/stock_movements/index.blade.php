<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mouvements de Stock & Consommables') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-movement')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                + Enregistrer un Mouvement
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if(session('success'))
                <div
                    class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Movement Filter -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('stock.index') }}" class="flex gap-4">
                    <select name="movement"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les mouvements</option>
                        <option value="Entrée" {{ request('movement') == 'Entrée' ? 'selected' : '' }}>Entrées (+)
                        </option>
                        <option value="Sortie" {{ request('movement') == 'Sortie' ? 'selected' : '' }}>Sorties (-)
                        </option>
                        <option value="Transfert" {{ request('movement') == 'Transfert' ? 'selected' : '' }}>Transferts
                        </option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                        Filtrer
                    </button>
                    @if(request('movement'))
                        <a href="{{ route('stock.index') }}"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Movements Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Équipement / Consommable</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type Mouvement</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Quantité</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Prix Achat</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Auteur / Date</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Commentaires</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($movements as $mv)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('equipment.show', $mv->equipment) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $mv->equipment->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($mv->movement === 'Entrée')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                            + Entrée
                                        </span>
                                    @elseif($mv->movement === 'Sortie')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                            - Sortie
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                            Transfert
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-100">
                                    {{ $mv->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-700 dark:text-gray-300">
                                    {{ number_format($mv->prix_achat ?? 0, 2, ',', ' ') }} MAD
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    <div>{{ $mv->user->name ?? 'Système' }}</div>
                                    <div class="text-gray-500">{{ $mv->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $mv->comment ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Aucun mouvement de stock enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>

    <!-- Create Movement Modal -->
    <x-modal name="create-movement" focusable>
        <form method="POST" action="{{ route('stock.store') }}" class="p-6"
            x-data="{
                selectedEquipment: '{{ old('equipment_id', '') }}',
                movementType: '{{ old('movement', 'Entrée') }}',
                quantity: parseInt('{{ old('quantity', 1) }}') || 1,
                sourceSiteId: '{{ old('source_site_id', '') }}',
                destinationSiteId: '{{ old('destination_site_id', '') }}',
                equipments: {
                    @foreach($equipments as $eq)
                        '{{ $eq->id }}': {
                            quantity: {{ $eq->quantity ?? 0 }},
                            isConsumable: {{ $eq->is_consumable ? 'true' : 'false' }},
                            siteId: '{{ $eq->site_id ?? '' }}'
                        },
                    @endforeach
                },
                get currentStock() {
                    return this.selectedEquipment && this.equipments[this.selectedEquipment] ? this.equipments[this.selectedEquipment].quantity : null;
                },
                get isConsumable() {
                    return this.selectedEquipment && this.equipments[this.selectedEquipment] ? this.equipments[this.selectedEquipment].isConsumable : false;
                },
                get isStockInsufficient() {
                    return (this.movementType === 'Sortie' || this.movementType === 'Transfert') && this.currentStock !== null && Number(this.quantity) > Number(this.currentStock);
                },
                get isSameSiteTransfer() {
                    return this.movementType === 'Transfert' && this.sourceSiteId && this.destinationSiteId && this.sourceSiteId === this.destinationSiteId;
                }
            }">
            @csrf
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Enregistrer un Mouvement de Stock</h3>

            <!-- Stock Insufficient Warning Alert -->
            <template x-if="isStockInsufficient">
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm flex items-center gap-2 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong>Quantité insuffisante !</strong> Stock disponible: <span x-text="currentStock"></span> unités. La quantité saisie (<span x-text="quantity"></span>) est trop élevée.
                    </div>
                </div>
            </template>

            <!-- Same Site Transfer Warning Alert -->
            <template x-if="isSameSiteTransfer">
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-md text-sm flex items-center gap-2 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-400">
                    <span>⚠️</span>
                    <div>
                        Le site d'origine et le site de destination doivent être différents.
                    </div>
                </div>
            </template>

            <div class="space-y-4">
                <div>
                    <x-input-label for="equipment_id" value="Équipement / Consommable *" />
                    <select name="equipment_id" x-model="selectedEquipment"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        required>
                        <option value="">Sélectionner un équipement...</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}">
                                {{ $eq->name }}
                                {{ $eq->is_consumable ? '(Consommable - Stock: ' . $eq->quantity . ')' : '(Unité)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <!-- Available Stock Indicator Badge -->
                    <div x-show="selectedEquipment && currentStock !== null" class="mt-1 text-xs text-gray-600 dark:text-gray-400 flex items-center gap-1">
                        <span>Stock disponible actuel :</span>
                        <span class="font-semibold text-indigo-600 dark:text-indigo-400" x-text="currentStock + ' unités'"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="movement" value="Type de mouvement *" />
                        <select name="movement" x-model="movementType"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                            required>
                            <option value="Entrée">Entrée (+ Stock)</option>
                            <option value="Sortie">Sortie (- Stock)</option>
                            <option value="Transfert">Transfert Inter-Sites</option>
                        </select>
                        @error('movement')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="quantity" value="Quantité *" />
                        <x-text-input name="quantity" type="number" min="1" class="mt-1 block w-full" x-model="quantity"
                            required />
                        @error('quantity')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Site Transfer Fields -->
                <div class="grid grid-cols-2 gap-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-md border border-gray-200 dark:border-gray-700" x-show="movementType === 'Transfert'" x-cloak>
                    <div>
                        <x-input-label for="source_site_id" value="Site d'origine" />
                        <select name="source_site_id" x-model="sourceSiteId"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                            <option value="">Sélectionner site d'origine...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        @error('source_site_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="destination_site_id" value="Site de destination *" />
                        <select name="destination_site_id" x-model="destinationSiteId" :required="movementType === 'Transfert'"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                            <option value="">Sélectionner site de destination...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                            @endforeach
                        </select>
                        @error('destination_site_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="prix_achat" value="Prix d'achat unitaire (MAD)" />
                    <x-text-input name="prix_achat" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('prix_achat', '0.00') }}" />
                    @error('prix_achat')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <x-input-label for="comment" value="Commentaire / Motif" />
                    <textarea name="comment"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        rows="2" placeholder="Numéro BL, bon de sortie, destination...">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-primary-button>Valider le mouvement</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>