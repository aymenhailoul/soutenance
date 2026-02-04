@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Stock Mouvements</h1>
            @if(request('date_from') || request('date_to'))
                <a href="{{ route('stock.movements.export', ['date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exporter
                </a>
            @else
                <a href="{{ route('stock.movements.export') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exporter
                </a>
            @endif
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('stock.movements') }}">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Date From -->
                    <div class="min-w-[160px]">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="date-from">
                            Date début
                        </label>
                        <input type="date" id="date-from" name="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                    </div>

                    <!-- Date To -->
                    <div class="min-w-[160px]">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="date-to">
                            Date fin
                        </label>
                        <input type="date" id="date-to" name="date_to" value="{{ request('date_to') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                    </div>

                    <!-- Apply Button -->
                    <div>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Appliquer
                        </button>
                    </div>

                    <!-- Clear Filter Button -->
                    @if(request('date_from') || request('date_to'))
                        <div>
                            <a href="{{ route('stock.movements') }}"
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
            </form>
        </div>

        <!-- Stock Movements Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Type</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Produit</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Quantité</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Montant</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Utilisateur</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Date</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Motif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($movements as $movement)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    @if ($movement->movement === 'Entrée')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entrée
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Sortie
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $movement->product->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    {{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">
                                    {{ number_format($movement->montant ?? 0, 2) }} DH
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300 capitalize">
                                    {{ $movement->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <div>{{ $movement->created_at->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    {{ $movement->comment ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-600 dark:text-gray-400">
                                    Aucun mouvement de stock trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
