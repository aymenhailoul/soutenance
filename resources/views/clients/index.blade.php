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
                <h2 class="text-lg font-semibold text-gray-900">Client List</h2>
                <x-button
                    variant="primary"
                    type="button"
                    onclick="document.getElementById('add-client-modal').classList.remove('hidden')"
                >
                    Add Client
                </x-button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Name</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Prenom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Phone</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Car Brand</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Matricule</th>
                            <th class="text-right px-6 py-3 text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $client->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
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
                                                    <h2 class="text-lg font-semibold text-gray-900">Update Client</h2>
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
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Name</label>
                                                                <input
                                                                    name="name"
                                                                    value="{{ old('name', $client->name) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Prenom</label>
                                                                <input
                                                                    name="prenom"
                                                                    value="{{ old('prenom', $client->prenom) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                                                                <input
                                                                    type="tel"
                                                                    name="phone"
                                                                    value="{{ old('phone', $client->phone) }}"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                    required
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Car Brand</label>
                                                                <input
                                                                    name="car_brand"
                                                                    value="{{ old('car_brand', $client->car_brand) }}"
                                                                    placeholder="Enter car brand"
                                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                                                />
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-900 mb-2">Matricule</label>
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
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"></path>
                                                </svg>
                                            </x-icon-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-600">
                                    No clients found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Client Modal -->
        <div
            id="add-client-modal"
            class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30"
        >
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Add Client</h2>
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
                                    Name <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Enter name"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Prenom <span class="text-red-600">*</span>
                                </label>
                                <input
                                    name="prenom"
                                    value="{{ old('prenom') }}"
                                    placeholder="Enter prenom"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Phone Number <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="Enter phone number"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Car Brand</label>
                                <input
                                    name="car_brand"
                                    value="{{ old('car_brand') }}"
                                    placeholder="Enter car brand"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Matricule</label>
                                <input
                                    name="matricule"
                                    value="{{ old('matricule') }}"
                                    placeholder="Enter matricule"
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
                                Cancel
                            </x-button>
                            <x-button variant="primary" type="submit">
                                Add Client
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
