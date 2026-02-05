<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        return view('stock.index', [
            'products' => Product::query()->where('type', 'Produit')->orderBy('name')->get(),
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');

        $products = Product::query()
            ->where('type', 'Produit')
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json($products);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'movement' => ['required', 'in:Entrée,Sortie'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        // Require comment for Sortie
        if ($request->input('movement') === 'Sortie' && empty($request->input('comment'))) {
            return back()->withErrors(['comment' => 'The motif field is required for stock exits.']);
        }

        // Pre-check stock availability for Sortie operations
        if ($validated['movement'] === 'Sortie') {
            $stock = DB::table('stock')
                ->where('product_id', $validated['product_id'])
                ->first();

            if (!$stock) {
                $product = Product::find($validated['product_id']);
                return back()->with('error', 'Cannot perform stock removal: "' . $product->name . '" has no stock available.');
            }

            if ($stock->quantity < $validated['quantity']) {
                $product = Product::find($validated['product_id']);
                return back()->with('error', 'Insufficient stock for "' . $product->name . '". Available: ' . $stock->quantity . ', Requested: ' . $validated['quantity'] . '.');
            }
        }

        DB::transaction(function () use ($validated) {
            // Get product for prix_achat (used for Entrée charges tracking)
            $product = Product::find($validated['product_id']);
            
            // Calculate montant (quantity * prix_achat)
            $montant = $validated['quantity'] * ($product->prix_achat ?? 0);
            
            // Create stock movement
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement' => $validated['movement'],
                'quantity' => $validated['quantity'],
                'prix_achat' => $validated['movement'] === 'Entrée' ? $product->prix_achat : null,
                'montant' => $montant,
                'user_id' => auth()->user()->id,
                'comment' => $validated['comment'] ?? null,
                'created_at' => now(),
            ]);

            // Update stock quantity
            $stock = DB::table('stock')
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($stock) {
                $newQuantity = $validated['movement'] === 'Entrée'
                    ? $stock->quantity + $validated['quantity']
                    : $stock->quantity - $validated['quantity'];

                DB::table('stock')
                    ->where('product_id', $validated['product_id'])
                    ->update(['quantity' => $newQuantity]);
            } else {
                // Create stock entry if it doesn't exist (only for Entrée)
                DB::table('stock')->insert([
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('stock.index')->with('success', 'Mouvement de stock enregistré avec succès.');
    }

    public function storeBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'movement' => ['required', 'in:Entrée,Sortie'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        // Require comment for Sortie
        if ($validated['movement'] === 'Sortie' && empty($validated['comment'])) {
            return back()->withErrors(['comment' => 'Le motif est requis pour les sorties.']);
        }

        // Pre-check stock availability for Sortie operations
        if ($validated['movement'] === 'Sortie') {
            foreach ($validated['items'] as $item) {
                $stock = DB::table('stock')
                    ->where('product_id', $item['product_id'])
                    ->first();

                $product = Product::find($item['product_id']);

                if (!$stock) {
                    return back()->with('error', 'Cannot perform stock removal: "' . $product->name . '" has no stock available.');
                }

                if ($stock->quantity < $item['quantity']) {
                    return back()->with('error', 'Insufficient stock for "' . $product->name . '". Available: ' . $stock->quantity . ', Requested: ' . $item['quantity'] . '.');
                }
            }
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $item) {
                // Get product for prix_achat
                $product = Product::find($item['product_id']);

                // Calculate montant (quantity * prix_achat)
                $montant = $item['quantity'] * ($product->prix_achat ?? 0);

                // Create stock movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'movement' => $validated['movement'],
                    'quantity' => $item['quantity'],
                    'prix_achat' => $validated['movement'] === 'Entrée' ? $product->prix_achat : null,
                    'montant' => $montant,
                    'user_id' => auth()->user()->id,
                    'comment' => $validated['comment'] ?? null,
                    'created_at' => now(),
                ]);

                // Update stock quantity
                $stock = DB::table('stock')
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($stock) {
                    $newQuantity = $validated['movement'] === 'Entrée'
                        ? $stock->quantity + $item['quantity']
                        : $stock->quantity - $item['quantity'];

                    DB::table('stock')
                        ->where('product_id', $item['product_id'])
                        ->update(['quantity' => $newQuantity]);
                } else {
                    // Create stock entry if it doesn't exist (only for Entrée)
                    DB::table('stock')->insert([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        $count = count($validated['items']);
        return redirect()->route('stock.index')->with('success', $count . ' mouvement(s) de stock enregistré(s) avec succès.');
    }

    public function movements(Request $request): View
    {
        $query = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return view('stock.movements', [
            'movements' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function exportMovements(Request $request)
    {
        $filters = $request->all();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StockMovementsExport($filters),
            'mouvements_stock_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
