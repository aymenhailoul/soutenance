<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditNote::with(['invoice', 'client', 'creator'])
            ->whereHas('invoice', function ($q) {
                $q->where('status', 'Finalized');
            })
            ->orderBy('created_at', 'desc');

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('credit_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('credit_date', '<=', $request->date_to);
        }

        if ($request->filled('credit_note_number')) {
            $query->where('credit_note_number', 'like', '%' . $request->credit_note_number . '%');
        }

        $creditNotes = $query->paginate(15)->withQueryString();
        $allClients = Client::orderBy('name')->get();

        return view('credit-notes.index', compact('creditNotes', 'allClients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'reason' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.invoice_item_id' => 'required|exists:invoice_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $invoice = Invoice::with('items.product')->findOrFail($validated['invoice_id']);

        // Verify invoice is finalized
        if ($invoice->status !== 'Finalized') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les factures finalisées peuvent avoir un retour'
            ], 400);
        }

        // Check if invoice type is 'produit' and if return would be for all products
        if ($invoice->type === 'produit') {
            $totalInvoiceQuantity = $invoice->items->sum('quantity');
            
            // Get already returned quantity for this invoice
            $alreadyReturnedQuantity = CreditNoteItem::whereHas('creditNote', function ($q) use ($invoice) {
                $q->where('invoice_id', $invoice->id);
            })->sum('quantity');

            $currentRequestQuantity = collect($validated['items'])->sum('quantity');

            if (($alreadyReturnedQuantity + $currentRequestQuantity) >= $totalInvoiceQuantity) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Impossible de faire un retour pour tous les produits de cette facture.'
                ], 400);
            }
        }



        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $itemsToCreate = [];

            // Validate and prepare items
            foreach ($validated['items'] as $item) {
                $invoiceItem = $invoice->items->find($item['invoice_item_id']);
                
                if (!$invoiceItem) {
                    throw new \Exception("Article de facture non trouvé");
                }

                // Calculate already returned quantity for this item
                $alreadyReturned = CreditNoteItem::where('invoice_item_id', $item['invoice_item_id'])
                    ->sum('quantity');

                $availableToReturn = $invoiceItem->quantity - $alreadyReturned;

                if ($item['quantity'] > $availableToReturn) {
                    throw new \Exception("Quantité de retour trop élevée pour {$invoiceItem->product->name}. Maximum: {$availableToReturn}");
                }

                // Calculate proportional discount
                $discountPerUnit = $invoiceItem->discount / $invoiceItem->quantity;
                $itemDiscount = $discountPerUnit * $item['quantity'];

                $itemTotal = ($invoiceItem->unit_price * $item['quantity']) - $itemDiscount;
                $totalAmount += $itemTotal;

                $itemsToCreate[] = [
                    'invoice_item_id' => $item['invoice_item_id'],
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $invoiceItem->unit_price,
                    'discount' => $itemDiscount,
                    'product' => $invoiceItem->product,
                ];
            }

            // Create credit note
            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'credit_date' => now(),
                'total_amount' => $totalAmount,
                'reason' => $validated['reason'] ?? null,
                'created_by' => auth()->user()->id,
            ]);

            // Create credit note items and restore stock
            foreach ($itemsToCreate as $item) {
                CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'invoice_item_id' => $item['invoice_item_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                ]);

                // Restore stock for physical products only
                if ($item['product']->type === 'Produit') {
                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'movement' => 'Entrée',
                        'quantity' => $item['quantity'],
                        'user_id' => auth()->user()->id,
                        'comment' => "Retour client - Avoir #{$creditNote->credit_note_number}",
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Avoir créé avec succès',
                'credit_note_id' => $creditNote->id,
                'credit_note_number' => $creditNote->credit_note_number,
                'pdf_url' => route('credit-notes.pdf', $creditNote->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Credit note creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf(CreditNote $creditNote)
    {
        $creditNote->load(['invoice', 'client', 'items.product', 'creator']);

        $pdf = Pdf::loadView('credit-notes.pdf', compact('creditNote'));

        return $pdf->download("Avoir_{$creditNote->credit_note_number}.pdf");
    }

    public function show(CreditNote $creditNote)
    {
        $creditNote->load(['items.product']);

        return response()->json([
            'items' => $creditNote->items
        ]);
    }

    public function export(Request $request)
    {
        $query = CreditNote::with(['invoice', 'client', 'creator'])
            ->whereHas('invoice', function ($q) {
                $q->where('status', 'Finalized');
            })
            ->orderBy('created_at', 'desc');

        // Apply same filters
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('credit_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('credit_date', '<=', $request->date_to);
        }
        
        if ($request->filled('credit_note_number')) {
            $query->where('credit_note_number', 'like', '%' . $request->credit_note_number . '%');
        }

        $creditNotes = $query->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RetoursExport($creditNotes),
            'retours_' . date('Y-m-d') . '.xlsx'
        );
    }
}
