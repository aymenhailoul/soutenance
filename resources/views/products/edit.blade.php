@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="p-8 max-w-4xl">

        <h1 class="text-3xl font-bold mb-6">Edit Product</h1>

        <form method="POST" action="{{ route('products.update', $product) }}"
            class="bg-white shadow rounded-lg p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
                    class="w-full rounded-lg border-gray-300" required>
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" id="type" class="w-full rounded-lg border-gray-300">
                    <option value="Produit" {{ old('type', $product->type) == 'Produit' ? 'selected' : '' }}>Produit</option>
                    <option value="Service" {{ old('type', $product->type) == 'Service' ? 'selected' : '' }}>Service</option>
                </select>
            </div>

            <!-- Prix Achat -->
            <div id="prixAchatField">
                <label class="block text-sm font-medium mb-1">Prix Achat</label>
                <input type="number" step="0.01" name="prix_achat" value="{{ old('prix_achat', $product->prix_achat) }}"
                    class="w-full rounded-lg border-gray-300" required>
            </div>

            <!-- Prix Vente -->
            <div>
                <label class="block text-sm font-medium mb-1">Prix Vente</label>
                <input type="number" step="0.01" name="prix_vente" value="{{ old('prix_vente', $product->prix_vente) }}"
                    class="w-full rounded-lg border-gray-300" required>
            </div>

            <!-- Serial Code -->
            <div id="serialCodeField">
                <label class="block text-sm font-medium mb-1">Serial Code</label>
                <input type="number" name="serial_code" value="{{ old('serial_code', $product->serial_code) }}"
                    class="w-full rounded-lg border-gray-300" required>
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
        document.addEventListener('DOMContentLoaded', () => {
            const type = document.getElementById('type');
            const achat = document.getElementById('prixAchatField');
            const serial = document.getElementById('serialCodeField');

            function toggle() {
                const isService = type.value === 'Service';
                achat.style.display = isService ? 'none' : 'block';
                serial.style.display = isService ? 'none' : 'block';
            }

            type.addEventListener('change', toggle);
            toggle();
        });
    </script>
@endsection