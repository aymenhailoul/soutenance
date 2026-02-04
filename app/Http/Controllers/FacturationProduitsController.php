<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CreditNoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturationProduitsController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return view('facturation.produits.index', compact('clients'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('type', 'Produit')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('serial_code', 'like', "%{$query}%");
            })
            ->take(20)
            ->get();

        // Calculate available stock for each product
        $products->transform(function ($product) {
            $product->stock = $product->stock;
            return $product;
        });

        return response()->json($products);
    }

    public function searchClients(Request $request)
    {
        $query = $request->get('q');
        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('prenom', 'like', "%{$query}%")
            ->take(20)
            ->get();

        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Check stock availability first
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }
            }

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $discount = $item['discount'] ?? 0;
                $lineTotal = ($item['unit_price'] * $item['quantity']) - $discount;
                $totalAmount += $lineTotal;
            }

            $invoice = Invoice::create([
                'type' => 'produit',
                'client_id' => $validated['client_id'],
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => $totalAmount,
                'status' => 'Finalized',
                'created_by' => auth()->user()->id,
            ]);


            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                ]);

                // Deduct stock for products
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'movement' => 'Sortie',
                    'quantity' => $item['quantity'],
                    'user_id' => auth()->user()->id,
                    'comment' => "Facture #{$invoice->invoice_number}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Facture de produits créée avec succès',
                'invoice_id' => $invoice->id,
                'pdf_url' => route('facturation.pdf', $invoice->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product invoice creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
