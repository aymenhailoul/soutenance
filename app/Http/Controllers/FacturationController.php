<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturationController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return view('facturation.index', compact('clients'));
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q');
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('serial_code', 'like', "%{$query}%")
            ->take(20)
            ->get();

        // Calculate available stock for each product
        $products->transform(function ($product) {
            $product->stock = $product->stock; // This uses the accessor in Product model
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
                if ($product->type === 'Product') {
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                    }
                }
            }

            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $discount = $item['discount'] ?? 0;
                $lineTotal = ($item['unit_price'] * $item['quantity']) - $discount;
                $totalAmount += $lineTotal;
            }

            $invoice = Invoice::create([
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

                // Deduct stock for physical products
                $product = Product::find($item['product_id']);
                if ($product->type === 'Product') {
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'movement' => 'Sortie',
                        'quantity' => $item['quantity'],
                        'user_id' => auth()->user()->id,
                        'comment' => "Facture #{$invoice->invoice_number}",
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Facture créée avec succès',
                'invoice_id' => $invoice->id,
                'pdf_url' => route('facturation.pdf', $invoice->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function validateInvoice(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'Draft') {
            return response()->json(['success' => false, 'message' => 'Invoice is already finalized'], 400);
        }

        DB::beginTransaction();
        try {
            // Check stock availability first
            foreach ($invoice->items as $item) {
                $product = $item->product;
                if ($product->type === 'Product') { // Only check stock for physical products
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                }
            }

            // Deduct stock
            foreach ($invoice->items as $item) {
                if ($item->product->type === 'Product') {
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'movement' => 'Sortie',
                        'quantity' => $item->quantity,
                        'user_id' => auth()->user()->id,
                        'comment' => "Invoice #{$invoice->invoice_number}",
                    ]);
                }
            }

            $invoice->update(['status' => 'Finalized']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice validated successfully',
                'pdf_url' => route('facturation.pdf', $invoice->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'creator']);
        
        $pdf = Pdf::loadView('facturation.pdf', compact('invoice'));
        
        return $pdf->download("Invoice_{$invoice->invoice_number}.pdf");
        // Or ->stream() if you prefer to open in browser first
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status !== 'Finalized') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les factures finalisées peuvent être annulées'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Restore stock for each item
            foreach ($invoice->items as $item) {
                if ($item->product->type === 'Product') {
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'movement' => 'Entrée',
                        'quantity' => $item->quantity,
                        'user_id' => auth()->user()->id,
                        'comment' => "Annulation Facture #{$invoice->invoice_number}",
                    ]);
                }
            }

            $invoice->update(['status' => 'Cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Facture annulée avec succès. Le stock a été restauré.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice cancellation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
            ], 500);
        }
    }
}
