@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Stock</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 px-4 py-3 text-green-800 dark:text-green-200">
                {{ session('success') }}
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

        <!-- Mode Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex gap-4">
                <button type="button" id="single-mode-btn" onclick="setMode('single')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors bg-blue-600 text-white">
                    Mouvement Simple
                </button>
                <button type="button" id="bulk-mode-btn" onclick="setMode('bulk')"
                    class="px-4 py-2 rounded-lg font-medium transition-colors bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                    Mouvements Multiples
                </button>
            </div>
        </div>

        <!-- Single Stock Management Form -->
        <div id="single-form-section" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('stock.store') }}" id="stock-form" class="space-y-6">
                @csrf

                <!-- Action Type Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="movement">
                        Entrée / Sortie<span class="text-red-600">*</span>
                    </label>
                    <select id="movement" name="movement"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" required
                        onchange="toggleFormFields()">
                        <option value="" class="text-gray-400">Selectionner un type de mouvement</option>
                        <option value="Entrée">Entrée</option>
                        <option value="Sortie">Sortie</option>
                    </select>
                </div>

                <!-- Product Autocomplete Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="product-search">
                        Produit <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="product-search" autocomplete="off" placeholder="Tapez pour chercher un produit..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400" disabled
                            oninput="searchProducts(this.value)" onfocus="showProductDropdown()"
                            onblur="setTimeout(() => hideProductDropdown(), 200)" />
                        <input type="hidden" id="product_id" name="product_id" required />
                        <div id="product-dropdown"
                            class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- Products will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="quantity">
                        Quantité <span class="text-red-600">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" placeholder="Enter quantity"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400" required
                        disabled />
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="comment">
                        Motif <span id="motif-required" class="text-red-600 hidden">*</span>
                    </label>
                    <textarea id="comment" name="comment" rows="3" placeholder="Motif de mouvement"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                        disabled></textarea>
                </div>

                <!-- Approve Button -->
                <div class="flex justify-end">
                    <x-button variant="primary" type="button" id="approve-btn" onclick="showApprovalModal()" disabled>
                        Valider
                    </x-button>
                </div>
            </form>
        </div>

        <!-- Bulk Stock Management Form -->
        <div id="bulk-form-section" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hidden">
            <form method="POST" action="{{ route('stock.store-bulk') }}" id="bulk-stock-form" class="space-y-6">
                @csrf

                <!-- Action Type Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="bulk-movement">
                        Entrée / Sortie<span class="text-red-600">*</span>
                    </label>
                    <select id="bulk-movement" name="movement"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" required
                        onchange="toggleBulkFormFields()">
                        <option value="" class="text-gray-400">Selectionner un type de mouvement</option>
                        <option value="Entrée">Entrée</option>
                        <option value="Sortie">Sortie</option>
                    </select>
                </div>

                <!-- Products List -->
                <div id="bulk-products-section" class="hidden">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Produits <span class="text-red-600">*</span>
                    </label>
                    
                    <!-- Add Product Row -->
                    <div class="flex gap-3 mb-4">
                        <div class="flex-1 relative">
                            <input type="text" id="bulk-product-search" autocomplete="off" placeholder="Tapez pour chercher un produit..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"
                                oninput="searchBulkProducts(this.value)" onfocus="showBulkProductDropdown()"
                                onblur="setTimeout(() => hideBulkProductDropdown(), 200)" />
                            <div id="bulk-product-dropdown"
                                class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            </div>
                        </div>
                        <input type="number" id="bulk-quantity-input" min="1" value="1" placeholder="Qté"
                            class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" />
                        <button type="button" onclick="addProductToList()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Products Table -->
                    <div id="selected-products-container" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="text-left px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Produit</th>
                                    <th class="text-center px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Quantité</th>
                                    <th class="text-right px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Action</th>
                                </tr>
                            </thead>
                            <tbody id="selected-products-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr id="no-products-row">
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Aucun produit ajouté. Utilisez la recherche ci-dessus pour ajouter des produits.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Comment -->
                <div id="bulk-comment-section" class="hidden">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2" for="bulk-comment">
                        Motif <span id="bulk-motif-required" class="text-red-600 hidden">*</span>
                    </label>
                    <textarea id="bulk-comment" name="comment" rows="3" placeholder="Motif de mouvement"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400 dark:placeholder-gray-400"></textarea>
                </div>

                <!-- Hidden inputs for products will be added here -->
                <div id="bulk-hidden-inputs"></div>

                <!-- Approve Button -->
                <div class="flex justify-end">
                    <x-button variant="primary" type="button" id="bulk-approve-btn" onclick="showBulkApprovalModal()" disabled>
                        Valider
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Approval Confirmation Modal (Single) -->
    <div id="approval-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirmer le mouvement de stock</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Êtes-vous sûr de vouloir effectuer ce mouvement de stock ?</p>
                <div class="flex justify-end gap-3">
                    <x-button variant="secondary" type="button" onclick="hideApprovalModal()">
                        Non
                    </x-button>
                    <x-button variant="primary" type="button" onclick="submitForm()">
                        Oui
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Approval Confirmation Modal -->
    <div id="bulk-approval-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Confirmer les mouvements de stock</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Êtes-vous sûr de vouloir effectuer <span id="bulk-count" class="font-bold"></span> mouvement(s) de stock ?</p>
                <div class="flex justify-end gap-3">
                    <x-button variant="secondary" type="button" onclick="hideBulkApprovalModal()">
                        Non
                    </x-button>
                    <x-button variant="primary" type="button" onclick="submitBulkForm()">
                        Oui
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Erreur de Stock</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6" id="error-message"></p>
                <div class="flex justify-end">
                    <x-button variant="primary" type="button" onclick="hideErrorModal()">
                        OK
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let products = @json($products);
        let searchTimeout;
        let highlightedIndex = -1;
        let currentFilteredProducts = [];
        let selectedBulkProducts = [];
        let currentMode = 'single';
        let selectedBulkProduct = null;

        // ========== MODE SWITCHING ==========
        function setMode(mode) {
            currentMode = mode;
            const singleBtn = document.getElementById('single-mode-btn');
            const bulkBtn = document.getElementById('bulk-mode-btn');
            const singleForm = document.getElementById('single-form-section');
            const bulkForm = document.getElementById('bulk-form-section');

            if (mode === 'single') {
                singleBtn.className = 'px-4 py-2 rounded-lg font-medium transition-colors bg-blue-600 text-white';
                bulkBtn.className = 'px-4 py-2 rounded-lg font-medium transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300';
                singleForm.classList.remove('hidden');
                bulkForm.classList.add('hidden');
            } else {
                bulkBtn.className = 'px-4 py-2 rounded-lg font-medium transition-colors bg-blue-600 text-white';
                singleBtn.className = 'px-4 py-2 rounded-lg font-medium transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300';
                bulkForm.classList.remove('hidden');
                singleForm.classList.add('hidden');
            }
        }

        // ========== SINGLE FORM FUNCTIONS ==========
        function toggleFormFields() {
            const movement = document.getElementById('movement').value;
            const isEnabled = movement !== '';

            const fields = ['product-search', 'quantity', 'comment'];
            const approveBtn = document.getElementById('approve-btn');

            // Toggle Required Asterisk
            const motifRequired = document.getElementById('motif-required');
            if (movement === 'Sortie') {
                motifRequired.classList.remove('hidden');
            } else {
                motifRequired.classList.add('hidden');
            }

            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                field.disabled = !isEnabled;
                if (!isEnabled) {
                    if (fieldId === 'product-search') {
                        field.value = '';
                    } else if (fieldId === 'quantity') {
                        field.value = '1';
                    } else if (fieldId === 'comment') {
                        field.value = '';
                    }
                }
            });

            approveBtn.disabled = !isEnabled;

            if (!isEnabled) {
                document.getElementById('product_id').value = '';
                hideProductDropdown();
            }
        }

        function searchProducts(query) {
            clearTimeout(searchTimeout);
            highlightedIndex = -1;

            if (query.length < 1) {
                hideProductDropdown();
                return;
            }

            searchTimeout = setTimeout(() => {
                const filtered = products.filter(p =>
                    p.name.toLowerCase().startsWith(query.toLowerCase())
                );

                currentFilteredProducts = filtered;
                displayProducts(filtered);
            }, 100);
        }

        function displayProducts(filteredProducts) {
            const dropdown = document.getElementById('product-dropdown');
            dropdown.innerHTML = '';
            currentFilteredProducts = filteredProducts;

            if (filteredProducts.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Aucun produit trouvé</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach((product, index) => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/50 cursor-pointer dropdown-item dark:text-gray-100';
                if (index === highlightedIndex) {
                    item.classList.add('bg-blue-100', 'dark:bg-blue-900');
                }
                item.dataset.index = index;
                item.textContent = product.name;
                item.onclick = () => selectProduct(product.id, product.name);
                dropdown.appendChild(item);
            });

            dropdown.classList.remove('hidden');
        }

        function updateHighlight() {
            const dropdown = document.getElementById('product-dropdown');
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
            const dropdown = document.getElementById('product-dropdown');
            if (dropdown.classList.contains('hidden')) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (highlightedIndex < currentFilteredProducts.length - 1) {
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
                if (highlightedIndex >= 0 && highlightedIndex < currentFilteredProducts.length) {
                    const product = currentFilteredProducts[highlightedIndex];
                    selectProduct(product.id, product.name);
                }
            } else if (event.key === 'Escape') {
                event.preventDefault();
                hideProductDropdown();
            }
        }

        function selectProduct(productId, productName) {
            document.getElementById('product-search').value = productName;
            document.getElementById('product_id').value = productId;
            hideProductDropdown();
        }

        function showProductDropdown() {
            highlightedIndex = -1;
            const query = document.getElementById('product-search').value;
            if (query.length > 0) {
                searchProducts(query);
            } else {
                displayProducts(products.slice(0, 20));
            }
        }

        function hideProductDropdown() {
            document.getElementById('product-dropdown').classList.add('hidden');
            highlightedIndex = -1;
        }

        // Attach keyboard listener
        document.getElementById('product-search').addEventListener('keydown', handleKeydown);

        function showApprovalModal() {
            // Validate form first
            const movement = document.getElementById('movement').value;
            const productId = document.getElementById('product_id').value;
            const quantity = document.getElementById('quantity').value;
            const comment = document.getElementById('comment').value;

            if (!movement || !productId || !quantity || parseInt(quantity) < 1) {
                alert('Veuillez remplir tous les champs requis.');
                return;
            }

            // Require comment for Sortie
            if (movement === 'Sortie' && !comment.trim()) {
                alert('Le motif est obligatoire pour les sorties.');
                return;
            }

            document.getElementById('approval-modal').classList.remove('hidden');
        }

        function hideApprovalModal() {
            document.getElementById('approval-modal').classList.add('hidden');
        }

        function submitForm() {
            document.getElementById('stock-form').submit();
        }

        // ========== BULK FORM FUNCTIONS ==========
        function toggleBulkFormFields() {
            const movement = document.getElementById('bulk-movement').value;
            const isEnabled = movement !== '';
            
            document.getElementById('bulk-products-section').classList.toggle('hidden', !isEnabled);
            document.getElementById('bulk-comment-section').classList.toggle('hidden', !isEnabled);

            // Toggle Required Asterisk for comment
            const motifRequired = document.getElementById('bulk-motif-required');
            if (movement === 'Sortie') {
                motifRequired.classList.remove('hidden');
            } else {
                motifRequired.classList.add('hidden');
            }

            updateBulkApproveButton();
        }

        function searchBulkProducts(query) {
            clearTimeout(searchTimeout);

            if (query.length < 1) {
                hideBulkProductDropdown();
                return;
            }

            searchTimeout = setTimeout(() => {
                const filtered = products.filter(p =>
                    p.name.toLowerCase().startsWith(query.toLowerCase()) &&
                    !selectedBulkProducts.some(sp => sp.id === p.id)
                );
                displayBulkProducts(filtered);
            }, 100);
        }

        function displayBulkProducts(filteredProducts) {
            const dropdown = document.getElementById('bulk-product-dropdown');
            dropdown.innerHTML = '';

            if (filteredProducts.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500 dark:text-gray-400">Aucun produit trouvé</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach(product => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/50 cursor-pointer dark:text-gray-100';
                item.textContent = product.name;
                item.onclick = () => selectBulkProduct(product);
                dropdown.appendChild(item);
            });

            dropdown.classList.remove('hidden');
        }

        function selectBulkProduct(product) {
            selectedBulkProduct = product;
            document.getElementById('bulk-product-search').value = product.name;
            hideBulkProductDropdown();
        }

        function showBulkProductDropdown() {
            const query = document.getElementById('bulk-product-search').value;
            if (query.length > 0) {
                searchBulkProducts(query);
            } else {
                const available = products.filter(p => !selectedBulkProducts.some(sp => sp.id === p.id));
                displayBulkProducts(available.slice(0, 20));
            }
        }

        function hideBulkProductDropdown() {
            document.getElementById('bulk-product-dropdown').classList.add('hidden');
        }

        function addProductToList() {
            if (!selectedBulkProduct) {
                alert('Veuillez sélectionner un produit.');
                return;
            }

            const quantity = parseInt(document.getElementById('bulk-quantity-input').value) || 1;
            if (quantity < 1) {
                alert('La quantité doit être au moins 1.');
                return;
            }

            // Add to list
            selectedBulkProducts.push({
                id: selectedBulkProduct.id,
                name: selectedBulkProduct.name,
                quantity: quantity
            });

            // Clear inputs
            document.getElementById('bulk-product-search').value = '';
            document.getElementById('bulk-quantity-input').value = '1';
            selectedBulkProduct = null;

            // Update table
            renderSelectedProducts();
            updateBulkApproveButton();
        }

        function renderSelectedProducts() {
            const tbody = document.getElementById('selected-products-body');
            const noProductsRow = document.getElementById('no-products-row');

            // Clear existing rows except the no-products placeholder
            tbody.innerHTML = '';

            if (selectedBulkProducts.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-products-row">
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            Aucun produit ajouté. Utilisez la recherche ci-dessus pour ajouter des produits.
                        </td>
                    </tr>
                `;
                return;
            }

            selectedBulkProducts.forEach((product, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm text-gray-900">${product.name}</td>
                    <td class="px-4 py-3 text-center">
                        <input type="number" min="1" value="${product.quantity}" 
                            onchange="updateProductQuantity(${index}, this.value)"
                            class="w-20 rounded border-gray-300 text-center text-sm" />
                    </td>
                    <td class="px-4 py-3 text-right">
                       <x-icon-button type="button" onclick="removeProduct(${index})" variant="danger">
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

                    
                `;
                tbody.appendChild(row);
            });
        }

        function updateProductQuantity(index, quantity) {
            const qty = parseInt(quantity) || 1;
            selectedBulkProducts[index].quantity = qty < 1 ? 1 : qty;
        }

        function removeProduct(index) {
            selectedBulkProducts.splice(index, 1);
            renderSelectedProducts();
            updateBulkApproveButton();
        }

        function updateBulkApproveButton() {
            const movement = document.getElementById('bulk-movement').value;
            const hasProducts = selectedBulkProducts.length > 0;
            document.getElementById('bulk-approve-btn').disabled = !movement || !hasProducts;
        }

        function showBulkApprovalModal() {
            const movement = document.getElementById('bulk-movement').value;
            const comment = document.getElementById('bulk-comment').value;

            if (!movement) {
                alert('Veuillez sélectionner un type de mouvement.');
                return;
            }

            if (selectedBulkProducts.length === 0) {
                alert('Veuillez ajouter au moins un produit.');
                return;
            }

            if (movement === 'Sortie' && !comment.trim()) {
                alert('Le motif est obligatoire pour les sorties.');
                return;
            }

            document.getElementById('bulk-count').textContent = selectedBulkProducts.length;
            document.getElementById('bulk-approval-modal').classList.remove('hidden');
        }

        function hideBulkApprovalModal() {
            document.getElementById('bulk-approval-modal').classList.add('hidden');
        }

        function submitBulkForm() {
            // Add hidden inputs for products
            const hiddenInputs = document.getElementById('bulk-hidden-inputs');
            hiddenInputs.innerHTML = '';

            selectedBulkProducts.forEach((product, index) => {
                hiddenInputs.innerHTML += `
                    <input type="hidden" name="items[${index}][product_id]" value="${product.id}" />
                    <input type="hidden" name="items[${index}][quantity]" value="${product.quantity}" />
                `;
            });

            document.getElementById('bulk-stock-form').submit();
        }

        // ========== ERROR MODAL ==========
        function showErrorModal(message) {
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-modal').classList.remove('hidden');
        }

        function hideErrorModal() {
            document.getElementById('error-modal').classList.add('hidden');
        }

        // Auto-show error modal if there's an error in the session
        @if (session('error'))
            document.addEventListener('DOMContentLoaded', function () {
                showErrorModal(@json(session('error')));
            });
        @endif
    </script>
@endsection