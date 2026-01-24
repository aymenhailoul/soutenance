<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
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
        $totalSales = Invoice::sum('total_amount');
        $totalInvoices = Invoice::count();

        return view('welcome', [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalSales' => $totalSales,
            'totalInvoices' => $totalInvoices,
        ]);
    }
}
