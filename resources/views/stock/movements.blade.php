@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Stock Mouvements</h1>
                <p class="text-gray-600 mt-1">Liste des mouvements de stock</p>
            </div>
            <a href="{{ route('stock.movements.export') }}">
                <x-button variant="success" type="button">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exporter
                </x-button>
            </a>
        </div>

        <!-- Stock Movements Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Type</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Produit</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Quantité</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Date</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Motif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($movements as $movement)
                            <tr class="hover:bg-gray-50">
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
                                    <div class="font-medium text-gray-900">{{ $movement->product->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div>{{ $movement->created_at->format('Y-m-d') }}</div>
                                    <div class="text-sm text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $movement->comment ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-600">
                                    No stock movements found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
