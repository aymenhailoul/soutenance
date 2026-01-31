<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Get statistics
        $totalProducts = Product::count();
        $totalClients = Client::count();
        
        // Calculate net sales (invoices minus credit notes/returns)
        $invoiceTotal = Invoice::where('status', 'Finalized')->sum('total_amount');
        // Only subtract credit notes from finalized invoices (not cancelled ones)
        $creditNoteTotal = CreditNote::whereHas('invoice', function ($query) {
            $query->where('status', 'Finalized');
        })->sum('total_amount');
        $totalSales = $invoiceTotal - $creditNoteTotal;
        
        $totalInvoices = Invoice::where('status', 'Finalized')->count();

        return view('welcome', [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalSales' => $totalSales,
            'totalInvoices' => $totalInvoices,
        ]);
    }
}
