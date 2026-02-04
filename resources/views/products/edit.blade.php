@extends('layouts.app')

@section('title', 'Modifier Produit')

@section('content')
    <div class="p-8 max-w-4xl">

        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">Modifier Produit</h1>

        <form method="POST" action="{{ route('products.update', $product) }}"
            class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-gray-100">Nom</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" required>
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-gray-100">Type</label>
                <select name="type" id="type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" onchange="toggleFields()">
                    <option value="Produit" {{ old('type', $product->type) == 'Produit' ? 'selected' : '' }}>Produit</option>
                    <option value="Service" {{ old('type', $product->type) == 'Service' ? 'selected' : '' }}>Service</option>
                </select>
            </div>

            <!-- Prix Achat -->
            <div id="prixAchatField">
                <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-gray-100">Prix Achat</label>
                <input type="number" step="0.01" name="prix_achat" id="prix_achat" value="{{ old('prix_achat', $product->prix_achat) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400">
            </div>

            <!-- Prix Vente -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-gray-100">Prix Vente</label>
                <input type="number" step="0.01" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400" required>
            </div>

            <!-- Serial Code -->
            <div id="serialCodeField">
                <label class="block text-sm font-medium mb-1 text-gray-900 dark:text-gray-100">Code</label>
                <input type="number" name="serial_code" id="serial_code" value="{{ old('serial_code', $product->serial_code) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-400 dark:focus:ring-blue-400">
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <x-button variant="primary" type="submit"
                    onclick="return confirm('Êtes-vous sûr de vouloir modifier ce produit?')">
                    Modifier
                </x-button>

                <x-button variant="secondary" href="{{ route('products.index') }}">
                    Annuler
                </x-button>
            </div>
        </form>
    </div>

    <script>
        function toggleFields() {
            const type = document.getElementById('type');
            const achatField = document.getElementById('prixAchatField');
            const serialField = document.getElementById('serialCodeField');
            const achatInput = document.getElementById('prix_achat');
            const serialInput = document.getElementById('serial_code');

            const isService = type.value === 'Service';
            
            achatField.style.display = isService ? 'none' : 'block';
            serialField.style.display = isService ? 'none' : 'block';

            if (isService) {
                achatInput.removeAttribute('required');
                serialInput.removeAttribute('required');
                achatInput.value = '';
                serialInput.value = '';
            } else {
                achatInput.setAttribute('required', 'required');
                serialInput.setAttribute('required', 'required');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleFields();
        });
    </script>
@endsection