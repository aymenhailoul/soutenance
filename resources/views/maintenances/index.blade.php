<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion de la Maintenance IT') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-maintenance')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                + Programmer / Déclarer une Maintenance
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

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('maintenances.index') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="status"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="Scheduled" {{ request('status') == 'Scheduled' ? 'selected' : '' }}>Planifiée
                        </option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>En cours
                        </option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Terminée
                        </option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Annulée
                        </option>
                    </select>

                    <select name="type"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les types</option>
                        <option value="Preventive" {{ request('type') == 'Preventive' ? 'selected' : '' }}>Préventive
                        </option>
                        <option value="Corrective" {{ request('type') == 'Corrective' ? 'selected' : '' }}>Corrective
                        </option>
                        <option value="Upgrade" {{ request('type') == 'Upgrade' ? 'selected' : '' }}>Mise à niveau /
                            Evolution</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                            Filtrer
                        </button>
                        @if(request('status') || request('type'))
                            <a href="{{ route('maintenances.index') }}"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium flex items-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Maintenances Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Intervention</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Équipement</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type / Prestataire</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date Prévue / Fin</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Coût (MAD)</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($maintenances as $maintenance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $maintenance->title }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <a href="{{ route('equipment.show', $maintenance->equipment) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ $maintenance->equipment->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">S/N:
                                        {{ $maintenance->equipment->serial_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <span
                                        class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $maintenance->type }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $maintenance->provider ?? 'Prestataire interne' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    <div>Prévue: {{ $maintenance->scheduled_at->format('d/m/Y H:i') }}</div>
                                    @if($maintenance->completed_at)
                                        <div class="text-green-600 dark:text-green-400">Fin:
                                            {{ $maintenance->completed_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($maintenance->cost, 2, ',', ' ') }} MAD
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($maintenance->status === 'Scheduled')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Planifiée</span>
                                    @elseif($maintenance->status === 'In Progress')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">En
                                            cours</span>
                                    @elseif($maintenance->status === 'Completed')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Terminée</span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">Annulée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="$dispatch('open-modal', 'edit-maint-{{ $maintenance->id }}')"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        Modifier
                                    </button>
                                    <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette maintenance ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <x-modal name="edit-maint-{{ $maintenance->id }}" focusable>
                                <form method="POST" action="{{ route('maintenances.update', $maintenance) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Modifier la
                                        maintenance</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="title" value="Titre / Description *" />
                                            <x-text-input name="title" type="text" class="mt-1 block w-full"
                                                value="{{ old('title', $maintenance->title) }}" required />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="type" value="Type de maintenance *" />
                                                <select name="type"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                                                    required>
                                                    <option value="Preventive" {{ $maintenance->type == 'Preventive' ? 'selected' : '' }}>Préventive</option>
                                                    <option value="Corrective" {{ $maintenance->type == 'Corrective' ? 'selected' : '' }}>Corrective</option>
                                                    <option value="Upgrade" {{ $maintenance->type == 'Upgrade' ? 'selected' : '' }}>Mise à niveau</option>
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label for="status" value="Statut *" />
                                                <select name="status"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                                                    required>
                                                    <option value="Scheduled" {{ $maintenance->status == 'Scheduled' ? 'selected' : '' }}>Planifiée</option>
                                                    <option value="In Progress" {{ $maintenance->status == 'In Progress' ? 'selected' : '' }}>En cours</option>
                                                    <option value="Completed" {{ $maintenance->status == 'Completed' ? 'selected' : '' }}>Terminée</option>
                                                    <option value="Cancelled" {{ $maintenance->status == 'Cancelled' ? 'selected' : '' }}>Annulée</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="provider" value="Prestataire / Intervenant" />
                                                <x-text-input name="provider" type="text" class="mt-1 block w-full"
                                                    value="{{ old('provider', $maintenance->provider) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="cost" value="Coût (MAD)" />
                                                <x-text-input name="cost" type="number" step="0.01"
                                                    class="mt-1 block w-full"
                                                    value="{{ old('cost', $maintenance->cost) }}" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="scheduled_at" value="Date prévue *" />
                                                <x-text-input name="scheduled_at" type="datetime-local"
                                                    class="mt-1 block w-full"
                                                    value="{{ old('scheduled_at', $maintenance->scheduled_at ? $maintenance->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                                                    required />
                                            </div>
                                            <div>
                                                <x-input-label for="completed_at" value="Date de fin" />
                                                <x-text-input name="completed_at" type="datetime-local"
                                                    class="mt-1 block w-full"
                                                    value="{{ old('completed_at', $maintenance->completed_at ? $maintenance->completed_at->format('Y-m-d\TH:i') : '') }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="notes" value="Remarques / Rapport" />
                                            <textarea name="notes"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                                                rows="3">{{ old('notes', $maintenance->notes) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                                        <x-primary-button>Mettre à jour</x-primary-button>
                                    </div>
                                </form>
                            </x-modal>

                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Aucune maintenance enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $maintenances->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-maintenance" focusable>
        <form method="POST" action="{{ route('maintenances.store') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Programmer / Déclarer une Maintenance
            </h3>
            <div class="space-y-4">
                <div>
                    <x-input-label for="equipment_id" value="Équipement concerné *" />
                    <select name="equipment_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        required>
                        <option value="">Sélectionner un équipement...</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}">{{ $eq->name }} (S/N: {{ $eq->serial_number ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="title" value="Titre / Motif de la maintenance *" />
                    <x-text-input name="title" type="text" class="mt-1 block w-full"
                        placeholder="Ex: Nettoyage et changement pâte thermique" required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="type" value="Type de maintenance *" />
                        <select name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                            required>
                            <option value="Preventive">Préventive</option>
                            <option value="Corrective">Corrective</option>
                            <option value="Upgrade">Mise à niveau</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" value="Statut *" />
                        <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                            required>
                            <option value="Scheduled">Planifiée</option>
                            <option value="In Progress">En cours</option>
                            <option value="Completed">Terminée</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="provider" value="Prestataire / Intervenant" />
                        <x-text-input name="provider" type="text" class="mt-1 block w-full"
                            placeholder="Ex: SAV Dell Morocco" />
                    </div>
                    <div>
                        <x-input-label for="cost" value="Coût estimé / réel (MAD)" />
                        <x-text-input name="cost" type="number" step="0.01" class="mt-1 block w-full" value="0.00" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="scheduled_at" value="Date prévue *" />
                        <x-text-input name="scheduled_at" type="datetime-local" class="mt-1 block w-full"
                            value="{{ now()->format('Y-m-d\TH:i') }}" required />
                    </div>
                    <div>
                        <x-input-label for="completed_at" value="Date de fin (si terminée)" />
                        <x-text-input name="completed_at" type="datetime-local" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <x-input-label for="notes" value="Remarques / Rapport d'intervention" />
                    <textarea name="notes"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        rows="3" placeholder="Détails des opérations effectuées..."></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-primary-button>Enregistrer la maintenance</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>