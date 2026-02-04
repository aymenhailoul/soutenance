@extends('layouts.app')

@section('title', 'Charges')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Charges</h1>
    </div>

    {{-- Success / Errors --}}
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

    <!-- Charges List Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col gap-4">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Liste des charges</h2>

                <div class="flex items-center gap-3">
                    
                    <!-- Export button (only visible when filtered) -->
                    @if($isFiltered)
                    <x-button
                        variant="success"
                        :href="route('charges.export', ['date_from' => $dateFrom, 'date_to' => $dateTo])">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                        Exporter Excel
                    </x-button>
                    @endif

                    <!-- Add Charge -->
                    <x-button
                        variant="primary"
                        type="button"
                        onclick="document.getElementById('add-charge-modal').classList.remove('hidden')">
                        Ajouter Charge
                    </x-button>

                </div>
            </div>

            <!-- Date Filter -->
            <form method="GET" action="{{ route('charges.index') }}" class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="date_from">
                        Du
                    </label>
                    <input 
                        type="date" 
                        id="date_from" 
                        name="date_from" 
                        value="{{ $dateFrom }}"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                    />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="date_to">
                        Au
                    </label>
                    <input 
                        type="date" 
                        id="date_to" 
                        name="date_to" 
                        value="{{ $dateTo }}"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                    />
                </div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Appliquer
                        </button>
                @if($isFiltered)
                <a href="{{ route('charges.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Effacer
                </a>
                @endif
            </form>

            @if($isFiltered)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Total des charges pour la période sélectionnée:</span>
                    <span class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($totalAmount, 2) }} DH</span>
                </div>
            </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Charge</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Montant</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Motif</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($charges as $charge)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            @if($charge['type'] === 'Entrée')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Entrée
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Supplémentaire
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-semibold">
                            {{ number_format($charge['amount'], 2) }} DH
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            {{ $charge['motif'] }}
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            <div>{{ $charge['created_at']->format('Y-m-d') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $charge['created_at']->format('H:i') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                            Aucune charge trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $charges->links() }}
    </div>
</div>

<!-- Add Charge Modal -->
<div
    id="add-charge-modal"
    class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajouter une charge</h2>
            <button
                type="button"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                onclick="document.getElementById('add-charge-modal').classList.add('hidden')">
                ✕
            </button>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('charges.store') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="from_modal" value="1" />

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Montant <span class="text-red-600">*</span></label>
                    <input
                        type="text"
                        name="amount"
                        value="{{ old('amount') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                        placeholder="0.00"
                        required 
                    />
                    @error('amount')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Motif <span class="text-red-600">*</span></label>
                    <input
                        type="text"
                        name="motif"
                        value="{{ old('motif') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                        placeholder="Ex: Loyer, Électricité, etc."
                        required 
                    />
                    @error('motif')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <x-button
                        variant="secondary"
                        type="button"
                        onclick="document.getElementById('add-charge-modal').classList.add('hidden')">
                        Annuler
                    </x-button>

                    <x-button variant="primary" type="submit">
                        Ajouter
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@if ($errors->any() && old('from_modal'))
<script>
    document.getElementById('add-charge-modal').classList.remove('hidden');
</script>
@endif

@endsection
