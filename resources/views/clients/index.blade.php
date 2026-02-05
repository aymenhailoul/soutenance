@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Clients</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-red-800 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 px-4 py-3 text-red-800 dark:text-red-200">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Clients List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liste des clients</h2>
                    <x-button
                        variant="primary"
                        type="button"
                        onclick="document.getElementById('add-client-modal').classList.remove('hidden')"
                    >
                        Ajouter un client
                    </x-button>
                </div>

                <!-- Client Search Dropdown -->
                <div class="flex items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="client-search">
                            Rechercher client
                        </label>
                        <div class="relative">
                            <input type="text" id="client-search" autocomplete="off"
                                placeholder="Taper nom, prénom ou téléphone"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                value="{{ request('client_id') ? $allClients->firstWhere('id', request('client_id'))?->name . ' ' . $allClients->firstWhere('id', request('client_id'))?->prenom : '' }}"
                                oninput="searchClients(this.value)" onfocus="showClientDropdown()"
                                onblur="setTimeout(() => hideClientDropdown(), 200)" />
                            <div id="client-dropdown"
                                class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto capitalize">
                                <!-- Clients will be populated here -->
                            </div>
                        </div>
                    </div>
                    @if(request('client_id'))
                        <div class="mt-6">
                            <a href="{{ route('clients.index') }}"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Effacer filtre
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Nom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Prénom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Téléphone</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Véhicules</th>
                            <th class="text-right px-12 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $client->name }}</div>
                                </td>
                                <td class="font-medium px-6 py-4 text-gray-900 dark:text-gray-100 capitalize">
                                    {{ $client->prenom }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    {{ $client->phone }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($client->vehicles->count() > 0)
                                        <button type="button" 
                                            onclick="toggleVehicles({{ $client->id }})"
                                            class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/30 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800/40 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            {{ $client->vehicles->count() }} véhicule(s)
                                        </button>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-sm">Aucun</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Add Vehicle button -->
                                        <x-icon-button
                                            title="Ajouter véhicule"
                                            variant="secondary"
                                            type="button"
                                            onclick="openAddVehicleModal({{ $client->id }}, '{{ $client->name }} {{ $client->prenom }}')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </x-icon-button>

                                        <!-- Edit button -->
                                        <x-icon-button
                                            title="Edit"
                                            variant="primary"
                                            type="button"
                                            onclick="document.getElementById('edit-client-{{ $client->id }}').classList.remove('hidden')"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </x-icon-button>

                                        <!-- Edit Modal -->
                                        <div
                                            id="edit-client-{{ $client->id }}"
                                            class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30"
                                        >
                                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4">
                                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Modifier Client</h2>
                                                    <button
                                                        type="button"
                                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                        onclick="document.getElementById('edit-client-{{ $client->id }}').classList.add('hidden')"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>

                                                <div class="p-6">
                                                    <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Nom</label>
                                                                <input
                                                                    name="name"
                                                                    value="{{ old('name', $client->name) }}"
                                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Prénom</label>
                                                                <input
                                                                    name="prenom"
                                                                    value="{{ old('prenom', $client->prenom) }}"
                                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Téléphone</label>
                                                                <input
                                                                    type="tel"
                                                                    name="phone"
                                                                    value="{{ old('phone', $client->phone) }}"
                                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                                                    required
                                                                />
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-end gap-3 pt-4">
                                                            <x-button
                                                                variant="secondary"
                                                                type="button"
                                                                onclick="document.getElementById('edit-client-{{ $client->id }}').classList.add('hidden')"
                                                            >
                                                                Annuler
                                                            </x-button>
                                                            <x-button variant="primary" type="submit">
                                                                Modifier
                                                            </x-button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete button -->
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                            onsubmit="return confirm('Supprimer le client {{ $client->name }} {{ $client->prenom }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button title="Delete" variant="danger" type="submit">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.75 6.16667C2.75 5.70644 3.09538 5.33335 3.52143 5.33335L6.18567 5.3329C6.71502 5.31841 7.18202 4.95482 7.36214 4.41691C7.36688 4.40277 7.37232 4.38532 7.39185 4.32203L7.50665 3.94993C7.5769 3.72179 7.6381 3.52303 7.72375 3.34536C8.06209 2.64349 8.68808 2.1561 9.41147 2.03132C9.59457 1.99973 9.78848 1.99987 10.0111 2.00002H13.4891C13.7117 1.99987 13.9056 1.99973 14.0887 2.03132C14.8121 2.1561 15.4381 2.64349 15.7764 3.34536C15.8621 3.52303 15.9233 3.72179 15.9935 3.94993L16.1083 4.32203C16.1279 4.38532 16.1333 4.40277 16.138 4.41691C16.3182 4.95482 16.8778 5.31886 17.4071 5.33335H19.9786C20.4046 5.33335 20.75 5.70644 20.75 6.16667C20.75 6.62691 20.4046 7 19.9786 7H3.52143C3.09538 7 2.75 6.62691 2.75 6.16667Z" fill="#fe4848"></path><path d="M11.6068 21.9998H12.3937C15.1012 21.9998 16.4549 21.9998 17.3351 21.1366C18.2153 20.2734 18.3054 18.8575 18.4855 16.0256L18.745 11.945C18.8427 10.4085 18.8916 9.6402 18.45 9.15335C18.0084 8.6665 17.2628 8.6665 15.7714 8.6665H8.22905C6.73771 8.6665 5.99204 8.6665 5.55047 9.15335C5.10891 9.6402 5.15777 10.4085 5.25549 11.945L5.515 16.0256C5.6951 18.8575 5.78515 20.2734 6.66534 21.1366C7.54553 21.9998 8.89927 21.9998 11.6068 21.9998Z" fill="#fe4848"></path></svg>
                                            </x-icon-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <!-- Vehicles Row (hidden by default) -->
                            @if($client->vehicles->count() > 0)
                            <tr id="vehicles-row-{{ $client->id }}" class="hidden bg-gray-50 dark:bg-gray-900">
                                <td colspan="5" class="px-6 py-4">
                                    <div class="pl-4 border-l-4 border-blue-300 dark:border-blue-600">
                                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Véhicules de {{ $client->name }} {{ $client->prenom }}</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($client->vehicles as $vehicle)
                                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-3 flex items-start justify-between">
                                                    <div>
                                                        <div class="font-medium text-gray-900 dark:text-gray-100" dir="ltr" style="unicode-bidi: bidi-override;">{{ $vehicle->plaque }}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $vehicle->marque }} {{ $vehicle->modele }}</div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-500">
                                                            {{ number_format($vehicle->kilometrage) }} km
                                                            @if($vehicle->annee) • {{ $vehicle->annee }} @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ route('vehicles.history', $vehicle) }}" 
                                                            class="p-1 text-blue-600 hover:text-blue-800" title="Historique">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 2 23 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                            </svg>
                                                        </a>
                                                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" 
                                                            onsubmit="return confirm('Supprimer ce véhicule?')" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 text-red-600 hover:text-red-800" title="Supprimer">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                                    Aucun client trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $clients->links() }}
        </div>

        <!-- Add Client Modal -->
        <div
            id="add-client-modal"
            class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30"
        >
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajouter un Client</h2>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        onclick="document.getElementById('add-client-modal').classList.add('hidden')"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Nom <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Entrer nom"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Prénom <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="prenom"
                                    value="{{ old('prenom') }}"
                                    placeholder="Entrer prénom"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Téléphone <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Entrer numéro de téléphone"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                    required
                                    inputmode="numeric"
                                    pattern="[0-9]{10}"
                                    maxlength="10"
                                    minlength="10"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <x-button
                                variant="secondary"
                                type="button"
                                onclick="document.getElementById('add-client-modal').classList.add('hidden')"
                            >
                                Annuler
                            </x-button>
                            <x-button variant="primary" type="submit">
                                Ajouter Client
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Vehicle Modal -->
        <div
            id="add-vehicle-modal"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30"
        >
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Ajouter un Véhicule pour <span id="add-vehicle-client-name" class="capitalize"></span>
                    </h2>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        onclick="document.getElementById('add-vehicle-modal').classList.add('hidden')"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="client_id" id="add-vehicle-client-id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Plaque d'immatriculation <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="plaque"
                                    dir="ltr"
                                    placeholder="Exemple: 12345-أ-78"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Marque <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="marque"
                                    placeholder="Toyota, Dacia, Renault..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Modèle</label>
                                <input
                                    name="modele"
                                    placeholder="Corolla, Logan, Clio..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Année</label>
                                <input
                                    type="number"
                                    name="annee"
                                    min="1900"
                                    max="{{ date('Y') + 1 }}"
                                    placeholder="{{ date('Y') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Kilométrage</label>
                                <input
                                    type="number"
                                    name="kilometrage"
                                    min="0"
                                    placeholder="0"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Carburant <span class="text-red-600">*</span>
                                </label>
                                <select
                                    name="carburant"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                                    required
                                >
                                    <option value="essence">Essence</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="hybride">Hybride</option>
                                    <option value="electrique">Électrique</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Couleur</label>
                                <input
                                    name="couleur"
                                    placeholder="Noir, Blanc, Gris..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <x-button
                                variant="secondary"
                                type="button"
                                onclick="document.getElementById('add-vehicle-modal').classList.add('hidden')"
                            >
                                Annuler
                            </x-button>
                            <x-button variant="primary" type="submit">
                                Ajouter Véhicule
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    // Client search dropdown functionality
    let allClients = @json($allClients);
    let searchTimeout;
    let highlightedIndex = -1;
    let currentFilteredClients = [];

    function searchClients(query) {
        clearTimeout(searchTimeout);
        highlightedIndex = -1;

        if (query.length < 1) {
            hideClientDropdown();
            return;
        }

        searchTimeout = setTimeout(() => {
            const filtered = allClients.filter(c => {
                const queryLower = query.toLowerCase();
                const nameMatch = c.name.toLowerCase().startsWith(queryLower);
                const prenomMatch = c.prenom && c.prenom.toLowerCase().startsWith(queryLower);
                const fullName = (c.name + ' ' + (c.prenom || '')).toLowerCase();
                const fullNameMatch = fullName.startsWith(queryLower);
                const phoneMatch = c.phone && c.phone.startsWith(query);
                return nameMatch || prenomMatch || fullNameMatch || phoneMatch;
            });

            currentFilteredClients = filtered;
            displayClients(filtered);
        }, 100);
    }

    function displayClients(filteredClients) {
        const dropdown = document.getElementById('client-dropdown');
        dropdown.innerHTML = '';
        currentFilteredClients = filteredClients;

        if (filteredClients.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Aucun client trouvé</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        filteredClients.forEach((client, index) => {
            const item = document.createElement('div');
            item.className = 'px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/50 cursor-pointer dropdown-item dark:text-gray-100';
            if (index === highlightedIndex) {
                item.classList.add('bg-blue-100', 'dark:bg-blue-900');
            }
            item.dataset.index = index;
            let display = `${client.name} ${client.prenom || ''}`;
            if (client.phone) {
                display += ` - ${client.phone}`;
            }
            item.textContent = display;
            item.onclick = () => selectClient(client.id, client.name, client.prenom);
            dropdown.appendChild(item);
        });

        dropdown.classList.remove('hidden');
    }

    function selectClient(clientId, clientName, clientPrenom) {
        document.getElementById('client-search').value = `${clientName} ${clientPrenom || ''}`.trim();
        hideClientDropdown();
        window.location.href = "{{ route('clients.index') }}?client_id=" + clientId;
    }

    function showClientDropdown() {
        highlightedIndex = -1;
        const query = document.getElementById('client-search').value;
        if (query.length > 0) {
            searchClients(query);
        } else {
            displayClients(allClients.slice(0, 20));
        }
    }

    function hideClientDropdown() {
        document.getElementById('client-dropdown').classList.add('hidden');
        highlightedIndex = -1;
    }

    // Vehicle Management Functions
    function toggleVehicles(clientId) {
        const row = document.getElementById('vehicles-row-' + clientId);
        if (row) {
            row.classList.toggle('hidden');
        }
    }

    function openAddVehicleModal(clientId, clientName) {
        document.getElementById('add-vehicle-client-id').value = clientId;
        document.getElementById('add-vehicle-client-name').textContent = clientName;
        document.getElementById('add-vehicle-modal').classList.remove('hidden');
    }

    // Attach keyboard listener
    document.getElementById('client-search').addEventListener('keydown', function(event) {
        const dropdown = document.getElementById('client-dropdown');
        if (dropdown.classList.contains('hidden')) return;

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (highlightedIndex < currentFilteredClients.length - 1) {
                highlightedIndex++;
                updateHighlight();
            }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (highlightedIndex > 0) {
                highlightedIndex--;
                updateHighlight();
            }
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (highlightedIndex >= 0 && highlightedIndex < currentFilteredClients.length) {
                const client = currentFilteredClients[highlightedIndex];
                selectClient(client.id, client.name, client.prenom);
            }
        } else if (event.key === 'Escape') {
            event.preventDefault();
            hideClientDropdown();
        }
    });

    function updateHighlight() {
        const dropdown = document.getElementById('client-dropdown');
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach((item, index) => {
            if (index === highlightedIndex) {
                item.classList.add('bg-blue-100', 'dark:bg-blue-900');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-blue-100', 'dark:bg-blue-900');
            }
        });
    }
</script>

@endsection
