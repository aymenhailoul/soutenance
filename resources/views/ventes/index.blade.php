@extends('layouts.app')

@section('title', 'Ventes')

@section('content')
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Ventes</h1>
            <a href="{{ route('ventes.export') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Export
            </a>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="search-input" placeholder="Rechercher par numéro de facture ou nom de client"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    oninput="filterInvoices()" />
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro de
                            Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="invoices-table-body">
                    @forelse ($invoices as $invoice)
                        <tr class="invoice-row hover:bg-gray-50 transition-colors"
                            data-invoice-number="{{ strtolower($invoice->invoice_number) }}"
                            data-client-prenom="{{ strtolower($invoice->client->prenom ?? '') }}"
                            data-client-name="{{ strtolower($invoice->client->name ?? '') }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->creator->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->invoice_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($invoice->client->prenom ?? '') . ' ' . ($invoice->client->name ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($invoice->total_amount, 2, '.', '') }} MAD
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Aucune facture trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="hidden bg-white rounded-lg shadow p-8 text-center text-gray-500 mt-6">
            Aucune facture ne correspond à votre recherche
        </div>
    </div>

    <!-- Invoice Details Modal -->
    <div id="invoice-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30"
        onclick="closeModalOnBackdrop(event)">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Détails de la Facture</h2>
                    <button onclick="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Invoice Info -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Numéro de Facture:</span>
                        <span class="text-sm font-semibold text-gray-900" id="modal-invoice-number"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Date de Création:</span>
                        <span class="text-sm text-gray-900" id="modal-invoice-date"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Client:</span>
                        <span class="text-sm text-gray-900" id="modal-client-name"></span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="border-t border-gray-200 pt-4">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qté</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Prix</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Remise</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items-body" class="divide-y divide-gray-100">
                            <!-- Items will be populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Total -->
                <div class="border-t-2 border-gray-300 mt-4 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                        <span class="text-2xl font-bold text-gray-900" id="modal-total"></span>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="closeInvoiceModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Exit
                    </button>
                    <button id="download-pdf-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allInvoices = @json($invoices);

        function filterInvoices() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.invoice-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const invoiceNumber = row.dataset.invoiceNumber;
                const clientPrenom = row.dataset.clientPrenom;
                const clientName = row.dataset.clientName;
                const fullClientName = (clientPrenom + ' ' + clientName).trim();

                if (invoiceNumber.includes(searchTerm) || fullClientName.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResults = document.getElementById('no-results');
            const table = document.querySelector('.bg-white.rounded-lg.shadow.overflow-hidden');

            if (visibleCount === 0 && searchTerm !== '') {
                noResults.classList.remove('hidden');
                table.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                table.classList.remove('hidden');
            }
        }

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
                                                <td class="px-3 py-2 text-sm text-gray-900">${item.product?.name || 'N/A'}</td>
                                                <td class="px-3 py-2 text-sm text-center text-gray-900">${item.quantity}</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900">${parseFloat(item.unit_price).toFixed(2)} MAD</td>
                                                <td class="px-3 py-2 text-sm text-right text-gray-900">${parseFloat(item.discount).toFixed(2)} MAD</td>
                                                <td class="px-3 py-2 text-sm text-right font-medium text-gray-900">${subtotal.toFixed(2)} MAD</td>
                                            </tr>
                                        `;
                        itemsBody.innerHTML += row;
                    });

                    // Set total
                    document.getElementById('modal-total').textContent = `${parseFloat(invoice.total_amount).toFixed(2)} MAD`;

                    // Set PDF download link
                    document.getElementById('download-pdf-btn').onclick = () => {
                        window.location.href = `/facturation/${invoice.id}/pdf`;
                    };

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
    </script>
@endsection