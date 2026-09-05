<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Sites & Implantation') }}
            </h2>
            <button @click="$dispatch('open-modal', 'create-site')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium shadow-sm transition">
                + Nouveau Site
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

            <!-- Search & Filters -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route('sites.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Rechercher par nom, code, ville, contact..."
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">

                    <select name="client_id"
                        class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Tous les Clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                            Filtrer
                        </button>
                        @if(request('search') || request('client_id'))
                            <a href="{{ route('sites.index') }}"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium flex items-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Sites Table -->
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Site</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Client</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ville / Adresse</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Contact Sur Site</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Équipements</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse($sites as $site)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">
                                    <div>{{ $site->name }}</div>
                                    @if($site->code)
                                    <div class="text-xs text-gray-500">Code: {{ $site->code }}</div>@endif
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">
                                    {{ $site->client->name ?? 'ISS Maroc (Interne)' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div>{{ $site->city ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $site->address }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    <div>{{ $site->contact_name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $site->contact_phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                        {{ $site->equipment_count }} équipement(s)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="$dispatch('open-modal', 'edit-site-{{ $site->id }}')"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                        Modifier
                                    </button>
                                    <form action="{{ route('sites.destroy', $site) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce site ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal for Site -->
                            <x-modal name="edit-site-{{ $site->id }}" focusable>
                                <form method="POST" action="{{ route('sites.update', $site) }}" class="p-6">
                                    @csrf
                                    @method('PUT')
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Modifier le site
                                    </h3>
                                    <div class="space-y-4">
                                        <div>
                                            <x-input-label for="name" value="Nom du Site *" />
                                            <x-text-input name="name" type="text" class="mt-1 block w-full"
                                                value="{{ old('name', $site->name) }}" required />
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="client_id" value="Client" />
                                                <select name="client_id"
                                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                                                    <option value="">ISS Maroc (Interne)</option>
                                                    @foreach($clients as $c)
                                                        <option value="{{ $c->id }}" {{ old('client_id', $site->client_id) == $c->id ? 'selected' : '' }}>
                                                            {{ $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label for="code" value="Code Site" />
                                                <x-text-input name="code" type="text" class="mt-1 block w-full"
                                                    value="{{ old('code', $site->code) }}" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="city" value="Ville" />
                                                <x-text-input name="city" type="text" class="mt-1 block w-full"
                                                    value="{{ old('city', $site->city) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="address" value="Adresse" />
                                                <x-text-input name="address" type="text" class="mt-1 block w-full"
                                                    value="{{ old('address', $site->address) }}" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <x-input-label for="contact_name" value="Responsable Site" />
                                                <x-text-input name="contact_name" type="text" class="mt-1 block w-full"
                                                    value="{{ old('contact_name', $site->contact_name) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="contact_phone" value="Téléphone Contact" />
                                                <x-text-input name="contact_phone" type="text" class="mt-1 block w-full"
                                                    value="{{ old('contact_phone', $site->contact_phone) }}" />
                                            </div>
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
                                    Aucun site trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $sites->links() }}
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-site" focusable>
        <form method="POST" action="{{ route('sites.store') }}" class="p-6">
            @csrf
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Nouveau Site / Implantation</h3>
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Nom du Site *" />
                    <x-text-input name="name" type="text" class="mt-1 block w-full" placeholder="Ex: Agence Technopark"
                        required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="client_id" value="Client Rattaché" />
                        <select name="client_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                            <option value="">ISS Maroc (Interne)</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="code" value="Code Site" />
                        <x-text-input name="code" type="text" class="mt-1 block w-full" placeholder="Ex: SITE-CAS-01" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="city" value="Ville" />
                        <x-text-input name="city" type="text" class="mt-1 block w-full" placeholder="Casablanca" />
                    </div>
                    <div>
                        <x-input-label for="address" value="Adresse" />
                        <x-text-input name="address" type="text" class="mt-1 block w-full"
                            placeholder="Route de Nouasseur" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="contact_name" value="Responsable Site" />
                        <x-text-input name="contact_name" type="text" class="mt-1 block w-full"
                            placeholder="M. Bennani" />
                    </div>
                    <div>
                        <x-input-label for="contact_phone" value="Téléphone Contact" />
                        <x-text-input name="contact_phone" type="text" class="mt-1 block w-full"
                            placeholder="+212 600 112233" />
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-primary-button>Créer le site</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>