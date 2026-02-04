<?php

namespace App\Http\Controllers;

use App\Models\CreditNoteItem;
use App\Models\Invoice;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturationController extends Controller
{

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
                if ($item->product->type === 'Produit') { // Only check stock for physical products
                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                }
            }

            // Deduct stock
            foreach ($invoice->items as $item) {
                if ($item->product->type === 'Produit') {
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
        $invoice->load(['client', 'vehicle', 'items.product', 'creator']);

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
            // Restore stock for each item, accounting for already returned quantities
            foreach ($invoice->items as $item) {
                if ($item->product->type === 'Produit') {
                    // Calculate quantity already returned via credit notes
                    $alreadyReturned = CreditNoteItem::where('invoice_item_id', $item->id)
                        ->sum('quantity');
                    
                    // Only restore the quantity that wasn't already returned
                    $quantityToRestore = $item->quantity - $alreadyReturned;
                    
                    if ($quantityToRestore > 0) {
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'movement' => 'Entrée',
                            'quantity' => $quantityToRestore,
                            'user_id' => auth()->user()->id,
                            'comment' => "Annulation Facture #{$invoice->invoice_number}",
                        ]);
                    }
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
