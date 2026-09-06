<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Affectations d\'Équipements IT') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-assignment')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                + Nouvelle Affectation
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

            <!-- Status Filter -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('assignments.index') }}" class="flex gap-4">
                    <select name="status"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Toutes les affectations</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>En cours (Actives)
                        </option>
                        <option value="Returned" {{ request('status') == 'Returned' ? 'selected' : '' }}>Restituées
                        </option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                        Filtrer
                    </button>
                    @if(request('status'))
                        <a href="{{ route('assignments.index') }}"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Assignments Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Équipement</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Affecté À</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Site / Client</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Dates</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('equipment.show', $assignment->equipment) }}"
                                        class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $assignment->equipment->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">S/N:
                                        {{ $assignment->equipment->serial_number ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $assignment->employee->name ?? 'Non spécifié' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div>Site: {{ $assignment->site->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Client: {{ $assignment->client->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs">
                                    <div>Début:
                                        {{ $assignment->assigned_at ? $assignment->assigned_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @if($assignment->returned_at)
                                        <div class="text-green-600 dark:text-green-400">Retour:
                                            {{ $assignment->returned_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($assignment->status === 'Active')
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                            En cours
                                        </span>
                                    @else
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Restitué
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($assignment->status === 'Active')
                                        <button @click="$dispatch('open-modal', 'return-assignment-{{ $assignment->id }}')"
                                            class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-medium shadow-sm">
                                            Marquer Restitué
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">Archivé</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Return Modal -->
                            @if($assignment->status === 'Active')
                                <x-modal name="return-assignment-{{ $assignment->id }}" focusable>
                                    <form method="POST" action="{{ route('assignments.return', $assignment) }}" class="p-6">
                                        @csrf
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Restitution
                                            d'équipement</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            Veuillez confirmer la date de restitution pour l'équipement
                                            <strong>{{ $assignment->equipment->name }}</strong>.
                                        </p>
                                        <div class="space-y-4">
                                            <div>
                                                <x-input-label for="returned_at" value="Date de restitution *" />
                                                <x-text-input name="returned_at" type="datetime-local" class="mt-1 block w-full"
                                                    value="{{ now()->format('Y-m-d\TH:i') }}" required />
                                            </div>
                                            <div>
                                                <x-input-label for="notes" value="Remarques / État au retour" />
                                                <textarea name="notes"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                                                    rows="3" placeholder="État du matériel, accessoires rendus..."></textarea>
                                            </div>
                                        </div>
                                        <div class="mt-6 flex justify-end gap-3">
                                            <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                                            <x-primary-button class="bg-amber-600 hover:bg-amber-700">Valider la
                                                restitution</x-primary-button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Aucune affectation trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>

    <!-- Create Assignment Modal -->
    <x-modal name="create-assignment" focusable>
        <form method="POST" action="{{ route('assignments.store') }}" class="p-6"
            x-data="{ targetType: '{{ old('client_id') ? 'client' : (old('site_id') ? 'site' : 'employee') }}' }">
            @csrf
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Nouvelle Affectation d'Équipement</h3>

            @if($equipments->isEmpty())
                <div class="p-4 mb-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-md dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-300 text-sm">
                    ⚠️ Aucun équipement disponible à l'affectation. Assurez-vous d'avoir des équipements non-consommables avec le statut <strong>Disponible</strong>.
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <x-input-label for="equipment_id" value="Équipement (Disponibles uniquement) *" />
                    <select name="equipment_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        required>
                        <option value="">Sélectionner un équipement...</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
                                {{ $eq->name }} (S/N: {{ $eq->serial_number ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-input-label for="assigned_at" value="Date d'affectation *" />
                    <x-text-input name="assigned_at" type="datetime-local" class="mt-1 block w-full"
                        value="{{ old('assigned_at', now()->format('Y-m-d\TH:i')) }}" required />
                    @error('assigned_at')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Selection (Radio Group) -->
                <div class="border-t border-b border-gray-200 dark:border-gray-700 py-3">
                    <x-input-label value="Bénéficiaire de l'affectation *" class="mb-2" />
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex items-center space-x-2 p-2 rounded border cursor-pointer text-sm"
                            :class="targetType === 'employee' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:border-indigo-500 font-semibold' : 'border-gray-300 dark:border-gray-700'">
                            <input type="radio" name="target_type_radio" value="employee" x-model="targetType" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 dark:text-gray-300">Employé</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 rounded border cursor-pointer text-sm"
                            :class="targetType === 'client' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:border-indigo-500 font-semibold' : 'border-gray-300 dark:border-gray-700'">
                            <input type="radio" name="target_type_radio" value="client" x-model="targetType" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 dark:text-gray-300">Client</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 rounded border cursor-pointer text-sm"
                            :class="targetType === 'site' ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:border-indigo-500 font-semibold' : 'border-gray-300 dark:border-gray-700'">
                            <input type="radio" name="target_type_radio" value="site" x-model="targetType" class="text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 dark:text-gray-300">Site</span>
                        </label>
                    </div>

                    <div class="mt-3">
                        <div x-show="targetType === 'employee'">
                            <x-input-label for="employee_id" value="Sélectionner l'Employé *" />
                            <select name="employee_id" :disabled="targetType !== 'employee'"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                                <option value="">Choisir un employé...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="targetType === 'client'">
                            <x-input-label for="client_id" value="Sélectionner le Client *" />
                            <select name="client_id" :disabled="targetType !== 'client'"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                                <option value="">Choisir un client...</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="targetType === 'site'">
                            <x-input-label for="site_id" value="Sélectionner le Site *" />
                            <select name="site_id" :disabled="targetType !== 'site'"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                                <option value="">Choisir un site...</option>
                                @foreach($sites as $s)
                                    <option value="{{ $s->id }}" {{ old('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->client->name ?? 'ISS' }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('target')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="notes" value="Remarques / Motif" />
                    <textarea name="notes"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm"
                        rows="2" placeholder="Motif de l'affectation, conditions...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-primary-button :disabled="$equipments->isEmpty()">Enregistrer l'affectation</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>