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
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');

        $products = Product::query()
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
            // Create stock movement
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement' => $validated['movement'],
                'quantity' => $validated['quantity'],
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

        return redirect()->route('stock.index')->with('success', 'Stock movement recorded successfully.');
    }

    public function movements(): View
    {
        $movements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stock.movements', [
            'movements' => $movements,
        ]);
    }

    public function exportMovements()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StockMovementsExport(),
            'mouvements_stock_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
