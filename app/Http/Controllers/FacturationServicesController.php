<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacturationServicesController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return view('facturation.services.index', compact('clients'));
    }

    public function searchServices(Request $request)
    {
        $query = $request->get('q');
        $services = Product::where('type', 'Service')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('serial_code', 'like', "%{$query}%");
            })
            ->take(20)
            ->get();

        return response()->json($services);
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
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'kilometrage' => 'nullable|integer|min:0',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $discount = $item['discount'] ?? 0;
                $lineTotal = ($item['unit_price'] * $item['quantity']) - $discount;
                $totalAmount += $lineTotal;
            }

            $invoice = Invoice::create([
                'type' => 'service',
                'client_id' => $validated['client_id'],
                'vehicle_id' => $validated['vehicle_id'] ?? null,
                'kilometrage' => $validated['kilometrage'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => $totalAmount,
                'status' => 'Finalized',
                'created_by' => auth()->user()->id,
            ]);

            // Update vehicle kilometrage if provided
            if (!empty($validated['vehicle_id']) && !empty($validated['kilometrage'])) {
                Vehicle::where('id', $validated['vehicle_id'])
                    ->update(['kilometrage' => $validated['kilometrage']]);
            }

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                ]);
                // No stock deduction for services
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Facture de services créée avec succès',
                'invoice_id' => $invoice->id,
                'pdf_url' => route('facturation.pdf', $invoice->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service invoice creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
