@extends('layouts.app')

@section('title', 'Historique - ' . $vehicle->plaque)

@section('content')
    <div class="p-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('clients.index') }}?client_id={{ $vehicle->client_id }}" 
                class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour aux clients
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Historique du Véhicule</h1>
        </div>

        <!-- Vehicle Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M19 4H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2zM15 12H9m6 4H9"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100" dir="ltr" style="unicode-bidi: bidi-override;">{{ $vehicle->plaque }}</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400">{{ $vehicle->marque }} {{ $vehicle->modele }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Propriétaire</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $vehicle->client->name }} {{ $vehicle->client->prenom }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Kilométrage</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($vehicle->kilometrage) }} km</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Année</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $vehicle->annee ?? '-' }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Carburant</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $vehicle->carburant }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service History -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Historique des Services 
                    <span class="text-gray-500 dark:text-gray-400 font-normal">({{ $invoices->total() }} intervention(s))</span>
                </h3>
            </div>

            @if($invoices->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($invoices as $invoice)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $invoice->invoice_date->format('d/m/Y') }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->invoice_number }}</span>
                                        @if($invoice->kilometrage)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                                {{ number_format($invoice->kilometrage) }} km
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->total_amount, 2) }} MAD</div>
                                    <a href="{{ route('facturation.pdf', $invoice) }}" target="_blank" 
                                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        Télécharger PDF
                                    </a>
                                </div>
                            </div>

                            <!-- Invoice Items -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                            <th class="text-left pb-2">Article</th>
                                            <th class="text-center pb-2 w-20">Qté</th>
                                            <th class="text-right pb-2 w-28">Prix Unit.</th>
                                            <th class="text-right pb-2 w-28">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($invoice->items as $item)
                                            <tr>
                                                <td class="py-2">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $item->product->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->product->type }}</div>
                                                </td>
                                                <td class="py-2 text-center text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                                                <td class="py-2 text-right text-gray-600 dark:text-gray-300">{{ number_format($item->unit_price, 2) }} MAD</td>
                                                <td class="py-2 text-right font-medium text-gray-900 dark:text-gray-100">
                                                    {{ number_format(($item->quantity * $item->unit_price) - $item->discount, 2) }} MAD
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-lg">Aucun service enregistré pour ce véhicule.</p>
                    <p class="text-sm mt-2">Les services apparaîtront ici après la création de factures.</p>
                </div>
            @endif
        </div>

        @if($invoices->total() > 0)
            <!-- Service Statistics -->
            @php
                $totalSpent = \App\Models\Invoice::where('vehicle_id', $vehicle->id)->where('status', 'Finalized')->sum('total_amount');
                $lastVisit = \App\Models\Invoice::where('vehicle_id', $vehicle->id)->where('status', 'Finalized')->orderBy('invoice_date', 'desc')->first();
                $totalVisits = \App\Models\Invoice::where('vehicle_id', $vehicle->id)->where('status', 'Finalized')->count();
            @endphp
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total dépensé</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ number_format($totalSpent, 2) }} MAD
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Dernière visite</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $lastVisit ? $lastVisit->invoice_date->format('d/m/Y') : '-' }}
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nombre de visites</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $totalVisits }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
