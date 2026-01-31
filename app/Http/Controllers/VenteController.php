<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentesExport;

class VenteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['client', 'items.product', 'creator'])
            ->orderBy('invoice_date', 'desc');

        // Filter by client if specified
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Filter by invoice number
        if ($request->has('invoice_number') && $request->invoice_number) {
            $query->where('invoice_number', 'like', '%' . $request->invoice_number . '%');
        }

        $invoices = $query->paginate(10)->withQueryString();

        return view('ventes.index', [
            'invoices' => $invoices,
            'allClients' => Client::orderBy('name')->get(),
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product', 'creditNotes']);

        return response()->json([
            'invoice' => $invoice,
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new VentesExport($request->client_id, $request->date_from, $request->date_to, $request->invoice_number),
            'ventes_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
