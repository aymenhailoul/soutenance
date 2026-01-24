@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <div class="p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Tableau de Bord</h1>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-2">Bonjour, {{ auth()->user()->name }}</h2>
            <p class="text-gray-600">Voici un aperçu de vos opérations commerciales</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Produits</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            @if(in_array(auth()->user()->role, ['Admin', 'Super User']))
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-600 text-sm font-medium">Total Ventes</p>
                            <p class="text-2xl font-bold text-gray-900 break-words">{{ number_format($totalSales, 2, '.', '') }}
                                MAD</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Active Clients -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Clients</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalClients }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Facturations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Facturations</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalInvoices }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection