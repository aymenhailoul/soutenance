<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    // SHOW PRODUCT LIST + FILTERS
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by specific product_id (from dropdown search)
        if ($request->filled('product_id')) {
            $query->where('id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('serial_code', 'like', "%{$search}%");
            });
        }

        // Optional filters (type)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        // Get all products for the dropdown search (include serial_code for searching)
        $allProducts = Product::select('id', 'name', 'serial_code')->orderBy('name')->get();

        return view('products.index', compact('products', 'allProducts'));
    }


    // SHOW ADD PRODUCT FORM

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Produit,Service',
            'prix_achat' => 'nullable|required_if:type,Produit|regex:/^\d+(\.\d{1,2})?$/',
            'prix_vente' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'serial_code' => 'nullable|required_if:type,Produit|integer|unique:products,serial_code',
        ], [
            'prix_achat.required_if' => 'veuillez renseigner ce champ',
            'serial_code.required_if' => 'veuillez renseigner ce champ',
            'serial_code.unique' => 'Ce code série est déjà utilisé.',
            'prix_achat.regex' => 'Le format du prix d\'achat est invalide.',
            'prix_vente.regex' => 'Le format du prix de vente est invalide.',
            'serial_code.integer' => 'Le code série doit être un numéro.',
        ]);

        // If Service → force NULL values
        if ($data['type'] === 'Service') {
            $data['prix_achat'] = null;
            $data['serial_code'] = null;
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit ajouté avec succès');
    }

    // SHOW EDIT FORM
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // UPDATE PRODUCT
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Produit,Service',
            'prix_achat' => 'nullable|required_if:type,Produit|regex:/^\d+(\.\d{1,2})?$/',
            'prix_vente' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'serial_code' => 'nullable|required_if:type,Produit|integer|unique:products,serial_code,' . $product->id,
        ], [
            'prix_achat.required_if' => 'veuillez renseigner ce champ',
            'serial_code.required_if' => 'veuillez renseigner ce champ',
            'serial_code.unique' => 'Ce code série est déjà utilisé.',
            'prix_achat.regex' => 'Le format du prix d\'achat est invalide.',
            'prix_vente.regex' => 'Le format du prix de vente est invalide.',
            'serial_code.integer' => 'Le code série doit être un numéro.',
        ]);

        // If Service → force NULL values
        if ($data['type'] === 'Service') {
            $data['prix_achat'] = null;
            $data['serial_code'] = null;
        }

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit modifié avec succès');
    }

    // DELETE PRODUCT
    public function destroy(Product $product)
    {
        // Check if product has any linked data (Invoices or Stock Movements)
        $hasHistory = $product->invoiceItems()->exists() || $product->stockMovements()->exists();

        if ($hasHistory) {
            // Prevent deletion if linked data exists
            return redirect()
                ->route('products.index')
                ->with('error', 'Impossible de supprimer ce produit car il est lié à d\'autres enregistrements.');
        } else {
            // Force Delete (Permanent)
            $product->delete();
            $message = 'Produit supprimé avec succès.';
        }

        return redirect()
            ->route('products.index')
            ->with('success', $message);
    }

    // EXPORT PRODUCTS (xls)

    public function export(Request $request, $format)
    {
        $filters = $request->only('search', 'type');

        return Excel::download(
            new ProductsExport($filters),
            'products.' . $format
        );
    }
}
