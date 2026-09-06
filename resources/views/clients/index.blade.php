<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Clients / Entreprises') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-client')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                + Nouveau Client
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

            <!-- Search Bar -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('clients.index') }}" class="flex gap-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par nom, code, ICE, contact, ville..."
                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                        Filtrer
                    </button>
                    @if(request('search'))
                        <a href="{{ route('clients.index') }}"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium flex items-center">
                            Réinitialiser
                        </a>
                    @endif
                </form>
            </div>

            <!-- Clients Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Client</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Code / ICE</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Contact</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ville</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Sites</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $client->name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div>Code: {{ $client->code ?? '-' }}</div>
                                    @if($client->ice)
                                    <div class="text-xs text-gray-500">ICE: {{ $client->ice }}</div>@endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div>{{ $client->contact_person ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $client->phone }}
                                        {{ $client->email ? '• ' . $client->email : '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $client->city ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        {{ $client->sites_count }} site(s)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="$dispatch('open-modal', 'edit-client-{{ $client->id }}')"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        Modifier
                                    </button>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal for Client -->
                            <x-modal name="edit-client-{{ $client->id }}" focusable>
                                <form method="POST" action="{{ route('clients.update', $client) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Modifier le client
                                    </h3>
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="name" value="Nom de l'entreprise *" />
                                            <x-text-input name="name" type="text" class="mt-1 block w-full"
                                                value="{{ old('name', $client->name) }}" required />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="code" value="Code Client" />
                                                <x-text-input name="code" type="text" class="mt-1 block w-full"
                                                    value="{{ old('code', $client->code) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="ice" value="ICE" />
                                                <x-text-input name="ice" type="text" class="mt-1 block w-full"
                                                    value="{{ old('ice', $client->ice) }}" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="contact_person" value="Contact Principal" />
                                                <x-text-input name="contact_person" type="text" class="mt-1 block w-full"
                                                    value="{{ old('contact_person', $client->contact_person) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="phone" value="Téléphone" />
                                                <x-text-input name="phone" type="text" class="mt-1 block w-full"
                                                    value="{{ old('phone', $client->phone) }}" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="email" value="Email" />
                                                <x-text-input name="email" type="email" class="mt-1 block w-full"
                                                    value="{{ old('email', $client->email) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="city" value="Ville" />
                                                <x-text-input name="city" type="text" class="mt-1 block w-full"
                                                    value="{{ old('city', $client->city) }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <x-input-label for="address" value="Adresse" />
                                            <x-text-input name="address" type="text" class="mt-1 block w-full"
                                                value="{{ old('address', $client->address) }}" />
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
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Aucun client trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $clients->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-client" focusable>
        <form method="POST" action="{{ route('clients.store') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Nouveau Client / Entreprise</h3>
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Nom de l'entreprise *" />
                    <x-text-input name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" placeholder="Ex: AXA Assurance"
                        required />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="code" value="Code Client" />
                        <x-text-input name="code" type="text" class="mt-1 block w-full" value="{{ old('code') }}" placeholder="Ex: CLI-AXA-01" />
                        @error('code')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="ice" value="ICE" />
                        <x-text-input name="ice" type="text" class="mt-1 block w-full" value="{{ old('ice') }}" placeholder="Ex: 00123456789" />
                        @error('ice')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="contact_person" value="Contact Principal" />
                        <x-text-input name="contact_person" type="text" class="mt-1 block w-full" value="{{ old('contact_person') }}"
                            placeholder="M. Alami" />
                        @error('contact_person')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="phone" value="Téléphone" />
                        <x-text-input name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone') }}"
                            placeholder="+212 600 000000" />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}"
                            placeholder="contact@company.ma" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-input-label for="city" value="Ville" />
                        <x-text-input name="city" type="text" class="mt-1 block w-full" value="{{ old('city') }}" placeholder="Casablanca" />
                        @error('city')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <x-input-label for="address" value="Adresse" />
                    <x-text-input name="address" type="text" class="mt-1 block w-full" value="{{ old('address') }}"
                        placeholder="Bd Zerktouni, Casa" />
                    @error('address')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-primary-button>Créer le client</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>