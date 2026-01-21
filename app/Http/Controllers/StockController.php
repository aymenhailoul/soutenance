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

                // Prevent negative stock
                if ($newQuantity < 0) {
                    throw new \Exception('Insufficient stock quantity.');
                }

                DB::table('stock')
                    ->where('product_id', $validated['product_id'])
                    ->update(['quantity' => $newQuantity]);
            } else {
                // Create stock entry if it doesn't exist
                if ($validated['movement'] === 'Entrée') {
                    DB::table('stock')->insert([
                        'product_id' => $validated['product_id'],
                        'quantity' => $validated['quantity'],
                        'updated_at' => now(),
                    ]);
                } else {
                    throw new \Exception('Cannot perform Sortie: product has no stock.');
                }
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

    public function exportMovements(): Response
    {
        $movements = StockMovement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'stock_movements_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['Type', 'Product', 'Quantity', 'Date', 'Time', 'User', 'Comment']);

            // Data rows
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->movement,
                    $movement->product->name ?? 'N/A',
                    $movement->quantity,
                    $movement->created_at->format('Y-m-d'),
                    $movement->created_at->format('H:i'),
                    $movement->user->name ?? 'N/A',
                    $movement->comment ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
