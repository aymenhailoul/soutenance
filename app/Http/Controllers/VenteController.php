<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentesExport;

class VenteController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::with(['client', 'items.product'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(10);

        return view('ventes.index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items.product']);

        return response()->json([
            'invoice' => $invoice,
        ]);
    }

    public function export()
    {
        return Excel::download(new VentesExport, 'ventes_' . date('Y-m-d_His') . '.xlsx');
    }
}
