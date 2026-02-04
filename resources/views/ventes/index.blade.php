@extends('layouts.app')

@section('title', 'Ventes')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Ventes</h1>
            @if(request('client_id') || request('date_from') || request('date_to') || request('invoice_number'))
                <a href="{{ route('ventes.export', ['client_id' => request('client_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to'), 'invoice_number' => request('invoice_number')]) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export
                </a>
            @endif
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('ventes.index') }}" id="filter-form">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Client Filter -->
                    <div class="relative flex-1 min-w-[200px] max-w-md">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="client-search">
                            Filtrer par client
                        </label>
                        <input type="hidden" name="client_id" id="client-id-input" value="{{ request('client_id') }}">
                        <div class="relative">
                            <input type="text" id="client-search" autocomplete="off"
                                placeholder="Taper nom du client"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 capitalize dark:placeholder-gray-400"
                                value="{{ request('client_id') ? $allClients->firstWhere('id', request('client_id'))?->name . ' ' . $allClients->firstWhere('id', request('client_id'))?->prenom : '' }}"
                                oninput="searchClients(this.value)" onfocus="showClientDropdown()"
                                onblur="setTimeout(() => hideClientDropdown(), 200)" />
                            <div id="client-dropdown"
                                class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto capitalize">
                                <!-- Clients will be populated here -->
                            </div>
                        </div>
                    </div>

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

                    <!-- Invoice Number Search -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="invoice-number">
                            N° Facture
                        </label>
                        <input type="text" id="invoice-number" name="invoice_number" value="{{ request('invoice_number') }}"
                            placeholder="Rechercher..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400" />
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
                    @if(request('client_id') || request('date_from') || request('date_to') || request('invoice_number'))
                        <div>
                            <a href="{{ route('ventes.index') }}"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Effacer filtres
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Numéro de
                            Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="invoices-table-body">
                    @forelse ($invoices as $invoice)
                        <tr class="invoice-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                            data-invoice-number="{{ strtolower($invoice->invoice_number) }}"
                            data-client-prenom="{{ strtolower($invoice->client->prenom ?? '') }}"
                            data-client-name="{{ strtolower($invoice->client->name ?? '') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 capitalize">
                                {{ $invoice->creator->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $invoice->invoice_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 capitalize">
                                {{ ($invoice->client->name ?? 'N/A') . ' ' . ($invoice->client->prenom ?? '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ number_format($invoice->total_amount, 2, '.', '') }} MAD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($invoice->status === 'Finalized')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Finalisée
                                    </span>
                                @elseif($invoice->status === 'Draft')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Brouillon
                                    </span>
                                @elseif($invoice->status === 'Cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Annulée
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="showInvoiceDetails({{ $invoice->id }})"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Aucune facture trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div id="invoice-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30"
        onclick="closeModalOnBackdrop(event)"
        data-can-cancel="{{ auth()->user()->hasPageAccess('ventes.cancel') ? 'true' : 'false' }}"
        data-can-return="{{ auth()->user()->hasPageAccess('ventes.return') ? 'true' : 'false' }}">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Détails de la Facture</h2>
                    <button onclick="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Invoice Info -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Numéro de Facture:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="modal-invoice-number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Date de Création:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="modal-invoice-date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Client:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100 capitalize" id="modal-client-name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Statut:</span>
                        <span class="text-sm" id="modal-status"></span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produit</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qté</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Prix</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Remise</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <!-- Items will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="border-t-2 border-gray-300 dark:border-gray-600 mt-4 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total:</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100" id="modal-total"></span>
                    </div>
                </div>

                <!-- Credit Notes Section -->
                <div id="credit-notes-section" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Avoirs liés:</h4>
                    <div id="credit-notes-list" class="space-y-2"></div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="closeInvoiceModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Exit
                    </button>
                    <button id="create-return-btn"
                        class="hidden px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Créer un retour
                    </button>
                    <button id="cancel-invoice-btn"
                        class="hidden px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Annuler Facture
                    </button>
                    <button id="download-pdf-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Modal -->
    <div id="return-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/30"
        onclick="closeReturnModalOnBackdrop(event)">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Créer un Retour (Avoir)</h2>
                    <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Reference Info -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 mb-4">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Facture:</strong> <span id="return-invoice-number"></span>
                    </p>
                </div>

                <!-- Items to Return -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">Sélectionnez les articles à retourner:</label>
                    <div id="return-items-container" class="space-y-3"></div>
                </div>

                <!-- Reason -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="return-reason">Motif du retour (optionnel):</label>
                    <textarea id="return-reason" rows="2" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                        placeholder="Ex: Produit défectueux, Changement d'avis..."></textarea>
                </div>

                <!-- Total Credit -->
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Crédit:</span>
                        <span class="text-2xl font-bold text-red-600 dark:text-red-400" id="return-total">0.00 MAD</span>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3">
                    <button onclick="closeReturnModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Annuler
                    </button>
                    <button id="confirm-return-btn" onclick="submitReturn()"
                        class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Confirmer le Retour
                    </button>
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
                    // Also check full name
                    const fullName = (c.name + ' ' + (c.prenom || '')).toLowerCase();
                    const fullNameMatch = fullName.startsWith(queryLower);
                    return nameMatch || prenomMatch || fullNameMatch;
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

        function handleKeydown(event) {
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
        }

        function selectClient(clientId, clientName, clientPrenom) {
            document.getElementById('client-search').value = `${clientName} ${clientPrenom || ''}`.trim();
            document.getElementById('client-id-input').value = clientId;
            hideClientDropdown();
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

        // Attach keyboard listener
        document.getElementById('client-search').addEventListener('keydown', handleKeydown);

        function showInvoiceDetails(invoiceId) {
            fetch(`/ventes/${invoiceId}`)
                .then(response => response.json())
                .then(data => {
                    const invoice = data.invoice;

                    // Populate modal
                    document.getElementById('modal-invoice-number').textContent = invoice.invoice_number;

                    // Format created_at to show date and time
                    const createdAt = new Date(invoice.created_at);
                    const formattedDateTime = createdAt.toLocaleString('fr-FR', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                    document.getElementById('modal-invoice-date').textContent = formattedDateTime;
                    const clientFullName = (invoice.client?.prenom || '') + ' ' + (invoice.client?.name || 'N/A');
                    document.getElementById('modal-client-name').textContent = clientFullName.trim();

                    // Populate items
                    const itemsBody = document.getElementById('modal-items-body');
                    itemsBody.innerHTML = '';

                    invoice.items.forEach(item => {
                        const subtotal = (item.unit_price * item.quantity) - item.discount;
                        const row = `
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">${item.product?.name || 'N/A'}</td>
                                                <td class="px-3 py-2 text-sm text-center text-gray-900 dark:text-gray-100">${item.quantity}</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900 dark:text-gray-100">${parseFloat(item.unit_price).toFixed(2)} MAD</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900 dark:text-gray-100">${parseFloat(item.discount).toFixed(2)} MAD</td>
                                                <td class="px-3 py-2 text-sm text-right font-medium text-gray-900 dark:text-gray-100">${subtotal.toFixed(2)} MAD</td>
                                            </tr>
                                        `;
                        itemsBody.innerHTML += row;
                    });

                    // Set total
                    document.getElementById('modal-total').textContent = `${parseFloat(invoice.total_amount).toFixed(2)} MAD`;

                    // Set status badge
                    const statusEl = document.getElementById('modal-status');
                    if (invoice.status === 'Finalized') {
                        statusEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Finalisée</span>';
                    } else if (invoice.status === 'Draft') {
                        statusEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Brouillon</span>';
                    } else if (invoice.status === 'Cancelled') {
                        statusEl.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">Annulée</span>';
                    }

                    // Set PDF download link
                    document.getElementById('download-pdf-btn').onclick = () => {
                        window.location.href = `/facturation/${invoice.id}/pdf`;
                    };

                    // Show/hide cancel button based on status AND permissions
                    const cancelBtn = document.getElementById('cancel-invoice-btn');
                    const createReturnBtn = document.getElementById('create-return-btn');
                    const creditNotesSection = document.getElementById('credit-notes-section');
                    const modal = document.getElementById('invoice-modal');
                    const canCancel = modal.dataset.canCancel === 'true';
                    const canReturn = modal.dataset.canReturn === 'true';
                    
                    if (invoice.status === 'Finalized') {
                        // Only show cancel button if user has permission
                        if (canCancel) {
                            cancelBtn.classList.remove('hidden');
                            cancelBtn.onclick = () => cancelInvoice(invoice.id);
                        } else {
                            cancelBtn.classList.add('hidden');
                        }
                        
                        // Only show return button if user has permission AND invoice has returnable items
                        // If invoice has only 1 product with quantity 1, don't allow return (use cancel instead)
                        // Also hide return button for service invoices (services cannot be returned)
                        if (invoice.type === 'service') {
                            createReturnBtn.classList.add('hidden');
                        } else {
                            const returnableItems = invoice.items.filter(item => {
                                // Only count products (not services) with available quantity
                                if (item.product?.type === 'Service') return false;
                                const alreadyReturned = item.returned_quantity || 0;
                                const availableQty = item.quantity - alreadyReturned;
                                return availableQty > 0;
                            });
                            
                            // Check if the only returnable scenario is 1 item with quantity 1 (and no services)
                            const totalReturnableQty = returnableItems.reduce((sum, item) => {
                                const alreadyReturned = item.returned_quantity || 0;
                                return sum + (item.quantity - alreadyReturned);
                            }, 0);
                            
                            // Check if invoice has any services
                            const hasServices = invoice.items.some(item => item.product?.type === 'Service');
                            
                            // Only block return if: 1 product with qty 1 AND no services in invoice
                            const isSingleProductOnly = returnableItems.length === 1 && totalReturnableQty === 1 && !hasServices;
                            
                            const canDoReturn = canReturn && returnableItems.length > 0 && !isSingleProductOnly;
                            
                            if (canDoReturn) {
                                createReturnBtn.classList.remove('hidden');
                                createReturnBtn.onclick = () => openReturnModal(invoice);
                            } else {
                                createReturnBtn.classList.add('hidden');
                            }
                        }
                    } else {
                        cancelBtn.classList.add('hidden');
                        createReturnBtn.classList.add('hidden');
                    }

                    // Display credit notes if any
                    if (invoice.credit_notes && invoice.credit_notes.length > 0) {
                        creditNotesSection.classList.remove('hidden');
                        const creditNotesList = document.getElementById('credit-notes-list');
                        creditNotesList.innerHTML = '';
                        
                        invoice.credit_notes.forEach(cn => {
                            const cnDate = new Date(cn.credit_date).toLocaleDateString('fr-FR');
                            const cnHtml = `
                                <div class="flex items-center justify-between bg-orange-50 dark:bg-orange-900/20 rounded-lg px-3 py-2">
                                    <div>
                                        <span class="font-medium text-orange-800 dark:text-orange-300">${cn.credit_note_number}</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">${cnDate}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-red-600 dark:text-red-400 font-semibold">-${parseFloat(cn.total_amount).toFixed(2)} MAD</span>
                                        <a href="/credit-notes/${cn.id}/pdf" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            `;
                            creditNotesList.innerHTML += cnHtml;
                        });
                    } else {
                        creditNotesSection.classList.add('hidden');
                    }

                    // Show modal
                    document.getElementById('invoice-modal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching invoice details:', error);
                    alert('Erreur lors du chargement des détails de la facture');
                });
        }

        function closeInvoiceModal() {
            document.getElementById('invoice-modal').classList.add('hidden');
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'invoice-modal') {
                closeInvoiceModal();
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeInvoiceModal();
            }
        });

        // Cancel invoice function
        function cancelInvoice(invoiceId) {
            if (!confirm('Êtes-vous sûr de vouloir annuler cette facture? Le stock sera restauré.')) {
                return;
            }

            fetch(`/facturation/${invoiceId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error cancelling invoice:', error);
                alert('Erreur lors de l\'annulation de la facture');
            });
        }

        // ========== RETURN MODAL FUNCTIONS ==========
        let currentReturnInvoice = null;

        function openReturnModal(invoice) {
            currentReturnInvoice = invoice;
            document.getElementById('return-invoice-number').textContent = invoice.invoice_number;
            document.getElementById('return-reason').value = '';
            
            // Build items list
            const container = document.getElementById('return-items-container');
            container.innerHTML = '';
            
            invoice.items.forEach((item, index) => {
                // Skip services - they cannot be returned
                if (item.product?.type === 'Service') return;
                
                // Calculate already returned quantity (we'll need to track this)
                const alreadyReturned = item.returned_quantity || 0;
                const availableQty = item.quantity - alreadyReturned;
                
                if (availableQty <= 0) return; // Skip fully returned items
                
                const itemHtml = `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 return-item" data-item-id="${item.id}" data-unit-price="${item.unit_price}" data-discount="${item.discount}" data-original-qty="${item.quantity}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="return-item-${item.id}" class="return-item-checkbox w-4 h-4 text-orange-600 border-gray-300 dark:border-gray-600 rounded focus:ring-orange-500 bg-white dark:bg-gray-700" onchange="toggleReturnItem(this, ${item.id})">
                                <label for="return-item-${item.id}" class="cursor-pointer">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">${item.product?.name || 'N/A'}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">(${item.product?.type || 'Produit'})</span>
                                </label>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                ${parseFloat(item.unit_price).toFixed(2)} MAD × ${item.quantity}
                            </div>
                        </div>
                        <div class="mt-2 flex items-center gap-3 return-qty-container hidden">
                            <label class="text-sm text-gray-600 dark:text-gray-300">Quantité à retourner:</label>
                            <input type="number" min="1" max="${availableQty}" value="${availableQty}" 
                                class="return-qty-input w-20 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:border-orange-500 focus:ring-orange-500 dark:focus:ring-orange-400"
                                data-item-id="${item.id}" onchange="calculateReturnTotal()">
                            <span class="text-xs text-gray-500 dark:text-gray-400">(max: ${availableQty})</span>
                        </div>
                    </div>
                `;
                container.innerHTML += itemHtml;
            });
            
            // Reset total
            document.getElementById('return-total').textContent = '0.00 MAD';
            document.getElementById('confirm-return-btn').disabled = true;
            
            // Show return modal
            document.getElementById('return-modal').classList.remove('hidden');
        }

        function toggleReturnItem(checkbox, itemId) {
            const itemContainer = checkbox.closest('.return-item');
            const qtyContainer = itemContainer.querySelector('.return-qty-container');
            
            if (checkbox.checked) {
                qtyContainer.classList.remove('hidden');
            } else {
                qtyContainer.classList.add('hidden');
            }
            
            calculateReturnTotal();
        }

        function calculateReturnTotal() {
            let total = 0;
            let hasSelectedItems = false;
            
            document.querySelectorAll('.return-item-checkbox:checked').forEach(checkbox => {
                hasSelectedItems = true;
                const itemContainer = checkbox.closest('.return-item');
                const unitPrice = parseFloat(itemContainer.dataset.unitPrice);
                const originalDiscount = parseFloat(itemContainer.dataset.discount);
                const originalQty = parseInt(itemContainer.dataset.originalQty);
                const returnQty = parseInt(itemContainer.querySelector('.return-qty-input').value) || 0;
                
                // Calculate proportional discount
                const discountPerUnit = originalDiscount / originalQty;
                const itemDiscount = discountPerUnit * returnQty;
                
                const itemTotal = (unitPrice * returnQty) - itemDiscount;
                total += itemTotal;
            });
            
            document.getElementById('return-total').textContent = `${total.toFixed(2)} MAD`;
            document.getElementById('confirm-return-btn').disabled = !hasSelectedItems;
        }

        function closeReturnModal() {
            document.getElementById('return-modal').classList.add('hidden');
            currentReturnInvoice = null;
        }

        function closeReturnModalOnBackdrop(event) {
            if (event.target.id === 'return-modal') {
                closeReturnModal();
            }
        }

        function submitReturn() {
            if (!currentReturnInvoice) return;
            
            const items = [];
            document.querySelectorAll('.return-item-checkbox:checked').forEach(checkbox => {
                const itemContainer = checkbox.closest('.return-item');
                const itemId = parseInt(itemContainer.dataset.itemId);
                const returnQty = parseInt(itemContainer.querySelector('.return-qty-input').value) || 0;
                
                if (returnQty > 0) {
                    items.push({
                        invoice_item_id: itemId,
                        quantity: returnQty
                    });
                }
            });
            
            if (items.length === 0) {
                alert('Veuillez sélectionner au moins un article à retourner');
                return;
            }
            
            const reason = document.getElementById('return-reason').value;
            
            if (!confirm('Êtes-vous sûr de vouloir créer cet avoir? Le stock sera restauré pour les produits retournés.')) {
                return;
            }
            
            document.getElementById('confirm-return-btn').disabled = true;
            document.getElementById('confirm-return-btn').textContent = 'Création en cours...';
            
            fetch('/credit-notes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    invoice_id: currentReturnInvoice.id,
                    reason: reason,
                    items: items
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Avoir ${data.credit_note_number} créé avec succès!`);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                    document.getElementById('confirm-return-btn').disabled = false;
                    document.getElementById('confirm-return-btn').textContent = 'Confirmer le Retour';
                }
            })
            .catch(error => {
                console.error('Error creating credit note:', error);
                alert('Erreur lors de la création de l\'avoir');
                document.getElementById('confirm-return-btn').disabled = false;
                document.getElementById('confirm-return-btn').textContent = 'Confirmer le Retour';
            });
        }

        // Close return modal on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeReturnModal();
            }
        });
    </script>
@endsection