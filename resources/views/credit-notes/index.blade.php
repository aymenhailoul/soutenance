@extends('layouts.app')

@section('title', 'Retours')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Retours (Avoirs)</h1>
            @if(request('client_id') || request('date_from') || request('date_to') || request('credit_note_number'))
                <a href="{{ route('retours.export', ['client_id' => request('client_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to'), 'credit_note_number' => request('credit_note_number')]) }}"
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
            <form method="GET" action="{{ route('retours.index') }}" id="filter-form">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Client Filter -->
                    <div class="relative flex-1 min-w-[200px] max-w-md">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="client-search">
                            Filtrer par client
                        </label>
                        <input type="hidden" name="client_id" id="client-id-input" value="{{ request('client_id') }}">
                        <div class="relative">
                            <input type="text" id="client-search" autocomplete="off"
                                placeholder="Taper nom ou prénom du client"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                value="{{ request('client_id') ? $allClients->firstWhere('id', request('client_id'))?->name . ' ' . $allClients->firstWhere('id', request('client_id'))?->prenom : '' }}"
                                oninput="searchClients(this.value)" onfocus="showClientDropdown()"
                                onblur="setTimeout(() => hideClientDropdown(), 200)" />
                            <div id="client-dropdown"
                                class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto capitalize">
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

                    <!-- Credit Note Number Search -->
                    <div class="min-w-[200px]">
                        <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="credit-note-number">
                            N° Avoir
                        </label>
                        <input type="text" id="credit-note-number" name="credit_note_number" value="{{ request('credit_note_number') }}"
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
                    @if(request('client_id') || request('date_from') || request('date_to') || request('credit_note_number'))
                        <div>
                            <a href="{{ route('retours.index') }}"
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

        <!-- Credit Notes Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">N° Avoir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">N° Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 capitalize">
                    @forelse ($creditNotes as $creditNote)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $creditNote->creator->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $creditNote->credit_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600 dark:text-orange-400">
                                {{ $creditNote->credit_note_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $creditNote->invoice->invoice_number ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ ($creditNote->client->name ?? 'N/A') . ' ' . ($creditNote->client->prenom ?? '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">
                                -{{ number_format($creditNote->total_amount, 2, '.', '') }} MAD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-2">
                                    <button onclick="showCreditNoteDetails({{ $creditNote->id }})"
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
                                    <a href="{{ route('credit-notes.pdf', $creditNote->id) }}"
                                        class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Aucun avoir trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $creditNotes->links() }}
        </div>
    </div>

    <!-- Credit Note Details Modal -->
    <div id="credit-note-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30"
        onclick="closeModalOnBackdrop(event)">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Détails de l'Avoir</h2>
                    <button onclick="closeCreditNoteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Credit Note Info -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">N° Avoir:</span>
                        <span class="text-sm font-semibold text-orange-600 dark:text-orange-400" id="modal-credit-note-number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Facture associée:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="modal-invoice-number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Date:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="modal-credit-date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Client:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="modal-client-name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Motif:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="modal-reason"></span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Articles retournés:</h4>
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
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="border-t-2 border-gray-300 dark:border-gray-600 mt-4 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Crédit:</span>
                        <span class="text-2xl font-bold text-red-600 dark:text-red-400" id="modal-total"></span>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="closeCreditNoteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        Fermer
                    </button>
                    <a id="download-pdf-btn" href="#"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Credit notes data from server
        const creditNotesData = @json($creditNotes->items());

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
                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500">Aucun client trouvé</div>';
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
                item.textContent = display;
                item.onclick = () => selectClient(client.id, client.name, client.prenom);
                dropdown.appendChild(item);
            });

            dropdown.classList.remove('hidden');
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

        // Credit Note Details Modal
        function showCreditNoteDetails(creditNoteId) {
            const creditNote = creditNotesData.find(cn => cn.id === creditNoteId);
            if (!creditNote) {
                alert('Avoir non trouvé');
                return;
            }

            // Populate modal
            document.getElementById('modal-credit-note-number').textContent = creditNote.credit_note_number;
            document.getElementById('modal-invoice-number').textContent = creditNote.invoice?.invoice_number || 'N/A';
            
            const creditDate = new Date(creditNote.credit_date);
            document.getElementById('modal-credit-date').textContent = creditDate.toLocaleDateString('fr-FR');
            
            const clientFullName = (creditNote.client?.prenom || '') + ' ' + (creditNote.client?.name || 'N/A');
            document.getElementById('modal-client-name').textContent = clientFullName.trim();
            document.getElementById('modal-reason').textContent = creditNote.reason || 'Non spécifié';

            // We need to fetch items since they're not loaded by default
            fetch(`/retours/${creditNoteId}/details`)
                .then(response => response.json())
                .then(data => {
                    const itemsBody = document.getElementById('modal-items-body');
                    itemsBody.innerHTML = '';

                    data.items.forEach(item => {
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
                })
                .catch(error => {
                    console.error('Error fetching credit note details:', error);
                });

            // Set total
            document.getElementById('modal-total').textContent = `-${parseFloat(creditNote.total_amount).toFixed(2)} MAD`;

            // Set PDF download link
            document.getElementById('download-pdf-btn').href = `/credit-notes/${creditNote.id}/pdf`;

            // Show modal
            document.getElementById('credit-note-modal').classList.remove('hidden');
        }

        function closeCreditNoteModal() {
            document.getElementById('credit-note-modal').classList.add('hidden');
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'credit-note-modal') {
                closeCreditNoteModal();
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeCreditNoteModal();
            }
        });

        // Keyboard navigation for client dropdown
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
