@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Clients</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Clients List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Liste des clients</h2>
                <x-button
                    variant="primary"
                    type="button"
                    onclick="document.getElementById('add-client-modal').classList.remove('hidden')"
                >
                    Ajouter un client
                </x-button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Nom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Prénom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Téléphone</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Marque Voiture</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Matricule</th>
                            <th class="text-right px-12 py-3 text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 capitalize">{{ $client->name }}</div>
                                </td>
                                <td class="font-medium px-6 py-4 text-gray-900 capitalize">
                                    {{ $client->prenom }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $client->phone }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $client->car_brand ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $client->matricule ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
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
                                            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
                                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                                                    <h2 class="text-lg font-semibold text-gray-900">Modifier Client</h2>
                                                    <button
                                                        type="button"
                                                        class="text-gray-400 hover:text-gray-600"
                                                        onclick="document.getElementById('edit-client-{{ $client->id }}').classList.add('hidden')"
                                                    >
                                                        ✕
                                                    </button>
                                                </div>

                                                <div class="p-6">
                                                    <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Nom</label>
                                                                <input
                                                                    name="name"
                                                                    value="{{ old('name', $client->name) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Prénom</label>
                                                                <input
                                                                    name="prenom"
                                                                    value="{{ old('prenom', $client->prenom) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Téléphone</label>
                                                                <input
                                                                    type="tel"
                                                                    name="phone"
                                                                    value="{{ old('phone', $client->phone) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Marque Voiture</label>
                                                                <input
                                                                    name="car_brand"
                                                                    value="{{ old('car_brand', $client->car_brand) }}"
                                                                    placeholder="Enter car brand"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Matricule Voiture</label>
                                                                <input
                                                                    name="matricule"
                                                                    value="{{ old('matricule', $client->matricule) }}"
                                                                    placeholder="Enter matricule"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                />
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-end gap-3 pt-4">
                                                            <x-button
                                                                variant="secondary"
                                                                type="button"
                                                                onclick="document.getElementById('edit-client-{{ $client->id }}').classList.add('hidden')"
                                                            >
                                                                Cancel
                                                            </x-button>
                                                            <x-button variant="primary" type="submit">
                                                                Update
                                                            </x-button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete button -->
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                            onsubmit="return confirm('Delete client {{ $client->name }} {{ $client->prenom }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button title="Delete" variant="danger" type="submit">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M2.75 6.16667C2.75 5.70644 3.09538 5.33335 3.52143 5.33335L6.18567 5.3329C6.71502 5.31841 7.18202 4.95482 7.36214 4.41691C7.36688 4.40277 7.37232 4.38532 7.39185 4.32203L7.50665 3.94993C7.5769 3.72179 7.6381 3.52303 7.72375 3.34536C8.06209 2.64349 8.68808 2.1561 9.41147 2.03132C9.59457 1.99973 9.78848 1.99987 10.0111 2.00002H13.4891C13.7117 1.99987 13.9056 1.99973 14.0887 2.03132C14.8121 2.1561 15.4381 2.64349 15.7764 3.34536C15.8621 3.52303 15.9233 3.72179 15.9935 3.94993L16.1083 4.32203C16.1279 4.38532 16.1333 4.40277 16.138 4.41691C16.3182 4.95482 16.8778 5.31886 17.4071 5.33335H19.9786C20.4046 5.33335 20.75 5.70644 20.75 6.16667C20.75 6.62691 20.4046 7 19.9786 7H3.52143C3.09538 7 2.75 6.62691 2.75 6.16667Z" fill="#fe4848"></path> <path d="M11.6068 21.9998H12.3937C15.1012 21.9998 16.4549 21.9998 17.3351 21.1366C18.2153 20.2734 18.3054 18.8575 18.4855 16.0256L18.745 11.945C18.8427 10.4085 18.8916 9.6402 18.45 9.15335C18.0084 8.6665 17.2628 8.6665 15.7714 8.6665H8.22905C6.73771 8.6665 5.99204 8.6665 5.55047 9.15335C5.10891 9.6402 5.15777 10.4085 5.25549 11.945L5.515 16.0256C5.6951 18.8575 5.78515 20.2734 6.66534 21.1366C7.54553 21.9998 8.89927 21.9998 11.6068 21.9998Z" fill="#fe4848"></path> </g></svg>
                                            </x-icon-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-600">
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
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Ajouter un Client</h2>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600"
                        onclick="document.getElementById('add-client-modal').classList.add('hidden')"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Nom <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Entrer nom"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Prénom <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="prenom"
                                    value="{{ old('prenom') }}"
                                    placeholder="Entrer prénom"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Téléphone<span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Entrer numéro de téléphone"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Marque Voiture</label>
                                <input
                                    name="car_brand"
                                    value="{{ old('car_brand') }}"
                                    placeholder="Entrer marque de la voiture"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Matricule Voiture</label>
                                <input
                                    name="matricule"
                                    value="{{ old('matricule') }}"
                                    placeholder="Entrer matricule de la voiture"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
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
    </div>
@endsection
