@extends('layouts.app')

@section('title', 'Facturation')

@section('content')
    <div class="p-8" x-data="invoiceManager()">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle Facture</h1>
            <div class="text-sm text-gray-500">
                Date: <span class="font-medium text-gray-900">{{ date('d/m/Y') }}</span>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Invoice Items -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Product Search -->
                <div class="bg-white rounded-lg shadow p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ajouter un produit ou service</label>
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts()"
                            placeholder="Rechercher par nom ou code..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pl-10">


                        <!-- Search Results Dropdown -->
                        <div x-show="searchResults.length > 0"
                            class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-y-auto"
                            @click.away="searchResults = []">
                            <template x-for="product in searchResults" :key="product.id">
                                <div @click="addItem(product)"
                                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex justify-between items-center border-b border-gray-100 last:border-0">
                                    <div>
                                        <div class="font-medium text-gray-900" x-text="product.name"></div>
                                        <div class="text-xs text-gray-500">
                                            <span x-text="product.type"></span> |
                                            Stock: <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'"
                                                x-text="product.stock"></span>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900"
                                        x-text="formatPrice(product.prix_vente)"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Prix</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Qté</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Remise</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(item, index) in items" :key="item.product_id">
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="item.name"></div>
                                        <div class="text-xs text-gray-500" x-text="item.type"></div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500"
                                        x-text="formatPrice(item.unit_price)"></td>
                                    <td class="px-6 py-4">
                                        <input type="number" x-model.number="item.quantity" min="1"
                                            :max="item.type === 'Produit' ? item.max_stock : null"
                                            class="w-full text-center rounded-md border-gray-300 py-1 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" x-model.number="item.discount" min="0" step="0.01"
                                            class="w-full text-right rounded-md border-gray-300 py-1 text-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="0.00">
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900"
                                        x-text="formatPrice((item.unit_price * item.quantity) - item.discount)"></td>
                                    <td class="px-6 py-4 text-center">
                                        <x-icon-button @click="removeItem(index)" variant="danger">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                    stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M2.75 6.16667C2.75 5.70644 3.09538 5.33335 3.52143 5.33335L6.18567 5.3329C6.71502 5.31841 7.18202 4.95482 7.36214 4.41691C7.36688 4.40277 7.37232 4.38532 7.39185 4.32203L7.50665 3.94993C7.5769 3.72179 7.6381 3.52303 7.72375 3.34536C8.06209 2.64349 8.68808 2.1561 9.41147 2.03132C9.59457 1.99973 9.78848 1.99987 10.0111 2.00002H13.4891C13.7117 1.99987 13.9056 1.99973 14.0887 2.03132C14.8121 2.1561 15.4381 2.64349 15.7764 3.34536C15.8621 3.52303 15.9233 3.72179 15.9935 3.94993L16.1083 4.32203C16.1279 4.38532 16.1333 4.40277 16.138 4.41691C16.3182 4.95482 16.8778 5.31886 17.4071 5.33335H19.9786C20.4046 5.33335 20.75 5.70644 20.75 6.16667C20.75 6.62691 20.4046 7 19.9786 7H3.52143C3.09538 7 2.75 6.62691 2.75 6.16667Z"
                                                        fill="#fe4848"></path>
                                                    <path
                                                        d="M11.6068 21.9998H12.3937C15.1012 21.9998 16.4549 21.9998 17.3351 21.1366C18.2153 20.2734 18.3054 18.8575 18.4855 16.0256L18.745 11.945C18.8427 10.4085 18.8916 9.6402 18.45 9.15335C18.0084 8.6665 17.2628 8.6665 15.7714 8.6665H8.22905C6.73771 8.6665 5.99204 8.6665 5.55047 9.15335C5.10891 9.6402 5.15777 10.4085 5.25549 11.945L5.515 16.0256C5.6951 18.8575 5.78515 20.2734 6.66534 21.1366C7.54553 21.9998 8.89927 21.9998 11.6068 21.9998Z"
                                                        fill="#fe4848"></path>
                                                </g>
                                            </svg>
                                        </x-icon-button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Aucun produit ajouté à la facture.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Side: Client & Summary -->
            <div class="space-y-6">
                <!-- Client Selection -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Client</h3>
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher un client</label>
                            <input type="text" x-model="clientSearchQuery" @input.debounce.300ms="searchClients()"
                                @focus="if(clientSearchQuery.length >= 2) searchClients()"
                                placeholder="Rechercher par nom..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                x-show="!selectedClient">

                            <!-- Selected Client Display -->
                            <div x-show="selectedClient"
                                class="flex items-center justify-between w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2">
                                <div>
                                    <span class="font-medium text-gray-900"
                                        x-text="selectedClient ? selectedClient.name + ' ' + selectedClient.prenom : ''"></span>
                                    <span class="text-sm text-gray-500 ml-2"
                                        x-text="selectedClient ? selectedClient.phone : ''"></span>
                                </div>
                                <button type="button" @click="clearClient()" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Client Search Results Dropdown -->
                            <div x-show="clientResults.length > 0 && !selectedClient"
                                class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-y-auto"
                                @click.away="clientResults = []">
                                <template x-for="client in clientResults" :key="client.id">
                                    <div @click="selectClient(client)"
                                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0">
                                        <div class="font-medium text-gray-900" x-text="client.name + ' ' + client.prenom">
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="client.phone"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                    <div class="space-y-3 pb-4 border-b border-gray-200">
                        <div class="flex justify-between text-gray-600">
                            <span>Sous-total</span>
                            <span x-text="formatPrice(calculateSubtotal())"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Total Remises</span>
                            <span class="text-green-600" x-text="'- ' + formatPrice(calculateTotalDiscount())"></span>
                        </div>
                    </div>
                    <div class="pt-4 flex justify-between items-end mb-6">
                        <span class="text-lg font-bold text-gray-900">Total à payer</span>
                        <span class="text-2xl font-bold text-blue-600" x-text="formatPrice(calculateTotal())"></span>
                    </div>

                    <button @click="validateInvoice()" :disabled="loading || items.length === 0 || !selectedClientId"
                        class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex justify-center items-center gap-2">
                        <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Traitement...' : 'Créer la Facture'"></span>
                    </button>
                    <p x-show="errorMessage" class="mt-2 text-sm text-red-600 text-center" x-text="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function invoiceManager() {
            return {
                searchQuery: '',
                searchResults: [],
                items: [],
                selectedClientId: '',
                selectedClient: null,
                clientSearchQuery: '',
                clientResults: [],
                loading: false,
                errorMessage: '',

                searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    fetch(`{{ route('facturation.search-products') }}?q=${this.searchQuery}`)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data;
                        });
                },

                searchClients() {
                    if (this.clientSearchQuery.length < 2) {
                        this.clientResults = [];
                        return;
                    }

                    fetch(`{{ route('facturation.search-clients') }}?q=${this.clientSearchQuery}`)
                        .then(res => res.json())
                        .then(data => {
                            this.clientResults = data;
                        });
                },

                selectClient(client) {
                    this.selectedClient = client;
                    this.selectedClientId = client.id;
                    this.clientSearchQuery = '';
                    this.clientResults = [];
                },

                clearClient() {
                    this.selectedClient = null;
                    this.selectedClientId = '';
                    this.clientSearchQuery = '';
                    this.clientResults = [];
                },

                addItem(product) {
                    const existingItem = this.items.find(item => item.product_id === product.id);

                    if (existingItem) {
                        existingItem.quantity++;
                    } else {
                        this.items.push({
                            product_id: product.id,
                            name: product.name,
                            type: product.type,
                            unit_price: parseFloat(product.prix_vente),
                            quantity: 1,
                            discount: 0,
                            max_stock: product.stock // For validation
                        });
                    }

                    this.searchQuery = '';
                    this.searchResults = [];
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                calculateSubtotal() {
                    return this.items.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
                },

                calculateTotalDiscount() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.discount) || 0), 0);
                },

                calculateTotal() {
                    return Math.max(0, this.calculateSubtotal() - this.calculateTotalDiscount());
                },

                formatPrice(value) {
                    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD' }).format(value);
                },

                validateInvoice() {
                    if (!this.selectedClientId || this.items.length === 0) return;

                    this.loading = true;
                    this.errorMessage = '';

                    fetch('{{ route("facturation.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            client_id: this.selectedClientId,
                            invoice_date: new Date().toISOString().slice(0, 10),
                            items: this.items
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Download PDF
                                window.location.href = data.pdf_url;
                                // Reset form
                                this.items = [];
                                this.selectedClientId = '';
                                alert('Facture créée avec succès !');
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            this.errorMessage = error.message;
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }
        }
    </script>
@endsection