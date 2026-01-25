@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Stock</h1>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stock Management Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('stock.store') }}" id="stock-form" class="space-y-6">
                @csrf

                <!-- Action Type Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="movement">
                        Entrée / Sortie<span class="text-red-600">*</span>
                    </label>
                    <select id="movement" name="movement"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        onchange="toggleFormFields()">
                        <option value="" class="text-gray-400">Selectionner un type de mouvement</option>
                        <option value="Entrée">Entrée</option>
                        <option value="Sortie">Sortie</option>
                    </select>
                </div>

                <!-- Product Autocomplete Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="product-search">
                        Produit <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="product-search" autocomplete="off" placeholder="Type to search products..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" disabled
                            oninput="searchProducts(this.value)" onfocus="showProductDropdown()"
                            onblur="setTimeout(() => hideProductDropdown(), 200)" />
                        <input type="hidden" id="product_id" name="product_id" required />
                        <div id="product-dropdown"
                            class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <!-- Products will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="quantity">
                        Quantité <span class="text-red-600">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" placeholder="Enter quantity"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required
                        disabled />
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2" for="comment">
                        Motif <span id="motif-required" class="text-red-600 hidden">*</span>
                    </label>
                    <textarea id="comment" name="comment" rows="3" placeholder="Motif de mouvement"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
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
    </div>

    <!-- Approval Confirmation Modal -->
    <div id="approval-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Confirm Stock Movement</h2>
                <p class="text-gray-600 mb-6">Are you sure you want to proceed with this stock movement?</p>
                <div class="flex justify-end gap-3">
                    <x-button variant="secondary" type="button" onclick="hideApprovalModal()">
                        No
                    </x-button>
                    <x-button variant="primary" type="button" onclick="submitForm()">
                        Yes
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Stock Error</h2>
                </div>
                <p class="text-gray-600 mb-6" id="error-message"></p>
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

            if (query.length < 1) {
                hideProductDropdown();
                return;
            }

            searchTimeout = setTimeout(() => {
                const filtered = products.filter(p =>
                    p.name.toLowerCase().startsWith(query.toLowerCase())
                );

                displayProducts(filtered);
            }, 100);
        }

        function displayProducts(filteredProducts) {
            const dropdown = document.getElementById('product-dropdown');
            dropdown.innerHTML = '';

            if (filteredProducts.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500">No products found</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach(product => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer';
                item.textContent = product.name;
                item.onclick = () => selectProduct(product.id, product.name);
                dropdown.appendChild(item);
            });

            dropdown.classList.remove('hidden');
        }

        function selectProduct(productId, productName) {
            document.getElementById('product-search').value = productName;
            document.getElementById('product_id').value = productId;
            hideProductDropdown();
        }

        function showProductDropdown() {
            const query = document.getElementById('product-search').value;
            if (query.length > 0) {
                searchProducts(query);
            } else {
                displayProducts(products.slice(0, 20));
            }
        }

        function hideProductDropdown() {
            document.getElementById('product-dropdown').classList.add('hidden');
        }

        function showApprovalModal() {
            // Validate form first
            const movement = document.getElementById('movement').value;
            const productId = document.getElementById('product_id').value;
            const quantity = document.getElementById('quantity').value;
            const comment = document.getElementById('comment').value;

            if (!movement || !productId || !quantity || parseInt(quantity) < 1) {
                alert('Please fill in all required fields.');
                return;
            }

            // Require comment for Sortie
            if (movement === 'Sortie' && !comment.trim()) {
                alert('The motif (comment) field is required for stock exits.');
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