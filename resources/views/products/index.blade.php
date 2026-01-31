@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Produits et Services</h1>
        </div>

        {{-- Success / Errors (same style as clients page) --}}
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

        <!-- Products List Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-gray-900">Liste des produits et services</h2>

                    <div class="flex items-center gap-3">

                        <!-- Export button -->
                        <x-button variant="success" :href="route('products.export', ['format' => 'xlsx'] + request()->query())">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Exporter Excel
                        </x-button>

                        <!-- Add Product-->
                        @if(auth()->user()->hasPageAccess('products.create'))
                            <x-button variant="primary" type="button"
                                onclick="document.getElementById('add-product-modal').classList.remove('hidden')">
                                Ajouter Produit
                            </x-button>
                        @endif

                    </div>
                </div>

                <!-- Product Search Dropdown -->
                <div class="flex items-center gap-3">
                    <div class="relative flex-1 max-w-md">
                        <label class="block text-sm font-semibold text-gray-900 mb-2" for="product-search">
                            Rechercher produit ou service
                        </label>
                        <div class="relative">
                            <input type="text" id="product-search" autocomplete="off"
                                placeholder="Taper nom ou code produit"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                value="{{ request('product_id') ? $allProducts->firstWhere('id', request('product_id'))?->name : '' }}"
                                oninput="searchProducts(this.value)" onfocus="showProductDropdown()"
                                onblur="setTimeout(() => hideProductDropdown(), 200)" />
                            <div id="product-dropdown"
                                class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Products will be populated here -->
                            </div>
                        </div>
                    </div>
                    @if(request('product_id'))
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Effacer filtre
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Nom</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Type</th>
                            @if(auth()->user()->hasPageAccess('products.show_cost'))
                                <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Prix Achat</th>
                            @endif
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Prix Vente</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Code</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Créé le</th>
                            <th class="text-left px-6 py-3 text-sm font-semibold text-gray-700">Stock</th>
                            @if(auth()->user()->hasPageAccess('products.create'))
                                <th class="text-right px-12 py-3 text-sm font-semibold text-gray-700">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody id="products-table-body" class="divide-y divide-gray-200">
                        @forelse ($products as $product)
                            @include('products.partials.row', ['product' => $product])
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-600">
                                    Aucun produit trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="add-product-modal" class="hidden fixed inset-0 z-40 flex items-center justify-center bg-black/30">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Ajouter produit</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600"
                    onclick="document.getElementById('add-product-modal').classList.add('hidden')">
                    ✕
                </button>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                    @csrf

                    {{-- hidden flag so we can detect the submit came from modal --}}
                    <input type="hidden" name="from_modal" value="1" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Nom <span
                                    class="text-red-600">*</span></label>
                            <input name="name" value="{{ old('name') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nom du produit" required />
                            @error('name')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Type <span
                                    class="text-red-600">*</span></label>
                            <select id="product-type" name="type" required onchange="toggleProductTypeFields()"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select</option>
                                <option value="Produit" {{ old('type') === 'Produit' ? 'selected' : '' }}>Produit</option>
                                <option value="Service" {{ old('type') === 'Service' ? 'selected' : '' }}>Service</option>
                            </select>
                            @error('type')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="prix-achat-wrapper">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Prix Achat</label>
                            <input id="prix-achat" type="text" name="prix_achat" value="{{ old('prix_achat') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="0.00" required />

                            @error('prix_achat')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Prix Vente<span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="prix_vente" value="{{ old('prix_vente') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="0.00" required />
                            @error('prix_vente')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="serial-code-wrapper" class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Code</label>
                            <input id="serial-code" name="serial_code" value="{{ old('serial_code') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                placeholder="..."  required/>

                            @error('serial_code')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <x-button variant="secondary" type="button"
                            onclick="document.getElementById('add-product-modal').classList.add('hidden')">
                            Annuler
                        </x-button>

                        <x-button variant="primary" type="submit">
                            Ajouter
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($errors->any() && old('from_modal'))
        <script>
            // Open modal when server-side validation failed for add-product modal
            document.getElementById('add-product-modal').classList.remove('hidden');
        </script>
    @endif

    <script>
        // Product search dropdown functionality
        let allProducts = @json($allProducts);
        let searchTimeout;
        let highlightedIndex = -1;
        let currentFilteredProducts = [];

        function searchProducts(query) {
            clearTimeout(searchTimeout);
            highlightedIndex = -1;

            if (query.length < 1) {
                hideProductDropdown();
                return;
            }

            searchTimeout = setTimeout(() => {
                const filtered = allProducts.filter(p => {
                    const queryLower = query.toLowerCase();
                    const nameMatch = p.name.toLowerCase().startsWith(queryLower);
                    const serialMatch = p.serial_code && p.serial_code.toString().startsWith(query);
                    return nameMatch || serialMatch;
                });

                currentFilteredProducts = filtered;
                displayProducts(filtered);
            }, 100);
        }

        function displayProducts(filteredProducts) {
            const dropdown = document.getElementById('product-dropdown');
            dropdown.innerHTML = '';
            currentFilteredProducts = filteredProducts;

            if (filteredProducts.length === 0) {
                dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500">No products found</div>';
                dropdown.classList.remove('hidden');
                return;
            }

            filteredProducts.forEach((product, index) => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 hover:bg-blue-50 cursor-pointer dropdown-item';
                if (index === highlightedIndex) {
                    item.classList.add('bg-blue-100');
                }
                item.dataset.index = index;
                // Show name and serial code if available
                item.textContent = product.serial_code
                    ? `${product.name} (${product.serial_code})`
                    : product.name;
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
                    item.classList.add('bg-blue-100');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-blue-100');
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
            hideProductDropdown();

            // Redirect to filtered view
            window.location.href = "{{ route('products.index') }}?product_id=" + productId;
        }

        function showProductDropdown() {
            highlightedIndex = -1;
            const query = document.getElementById('product-search').value;
            if (query.length > 0) {
                searchProducts(query);
            } else {
                displayProducts(allProducts.slice(0, 20));
            }
        }

        function hideProductDropdown() {
            document.getElementById('product-dropdown').classList.add('hidden');
            highlightedIndex = -1;
        }

        // Attach keyboard listener
        document.getElementById('product-search').addEventListener('keydown', handleKeydown);

        // Toggle Prix Achat and Code fields based on product type
        function toggleProductTypeFields() {
            const type = document.getElementById('product-type').value;
            const prixAchatWrapper = document.getElementById('prix-achat-wrapper');
            const serialCodeWrapper = document.getElementById('serial-code-wrapper');

            if (type === 'Service') {
                prixAchatWrapper.style.display = 'none';
                serialCodeWrapper.style.display = 'none';
                // Clear values when hiding
                document.getElementById('prix-achat').value = '';
                document.getElementById('serial-code').value = '';
            } else {
                prixAchatWrapper.style.display = 'block';
                serialCodeWrapper.style.display = 'block';
            }
        }

        // Run on page load to handle old() values
        document.addEventListener('DOMContentLoaded', function () {
            toggleProductTypeFields();
        });
    </script>


@endsection