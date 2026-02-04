<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\StockMovement;
use App\Exports\ChargesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ChargeController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Parse dates if provided
        $dateFromParsed = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : null;
        $dateToParsed = $dateTo ? Carbon::parse($dateTo)->endOfDay() : null;

        // Get stock entrées with calculated amount (exclude those from cancelled invoices)
        $entreesQuery = StockMovement::with('product')
            ->where('movement', 'Entrée')
            ->where(function ($q) {
                $q->whereNull('comment')
                    ->orWhere(function ($subQ) {
                        $subQ->where('comment', 'not like', '%Annulation%')
                             ->where('comment', 'not like', '%Retour%');
                    });
            })
            ->whereHas('product', function ($q) {
                $q->where('type', 'Produit');
            });

        if ($dateFromParsed && $dateToParsed) {
            $entreesQuery->whereBetween('created_at', [$dateFromParsed, $dateToParsed]);
        }

        $entrees = $entreesQuery->get()->map(function ($movement) {
            // Use stored prix_achat if available, fallback to current product price for old entries
            $prixAchat = $movement->prix_achat ?? $movement->product->prix_achat;
            return [
                'id' => 'entree_' . $movement->id,
                'type' => 'Entrée',
                'amount' => $prixAchat * $movement->quantity,
                'motif' => 'Entrée - ' . $movement->product->name . ' (x' . $movement->quantity . ')',
                'created_at' => $movement->created_at,
            ];
        });

        // Get custom charges
        $chargesQuery = Charge::query();

        if ($dateFromParsed && $dateToParsed) {
            $chargesQuery->whereBetween('created_at', [$dateFromParsed, $dateToParsed]);
        }

        $customCharges = $chargesQuery->get()->map(function ($charge) {
            return [
                'id' => 'charge_' . $charge->id,
                'type' => 'Supplémentaire',
                'amount' => $charge->amount,
                'motif' => $charge->motif,
                'created_at' => $charge->created_at,
            ];
        });

        // Combine and sort by date
        $allCharges = $entrees->concat($customCharges)
            ->sortByDesc('created_at')
            ->values();

        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = $allCharges->count();
        $items = $allCharges->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $isFiltered = $dateFrom && $dateTo;

        // Calculate total amount when filtered
        $totalAmount = $isFiltered ? $allCharges->sum('amount') : 0;

        return view('charges.index', [
            'charges' => $paginator,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'isFiltered' => $isFiltered,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'motif' => 'required|string|max:255',
        ]);

        Charge::create($data);

        return redirect()
            ->route('charges.index')
            ->with('success', 'Charge ajoutée avec succès');
    }

    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (!$dateFrom || !$dateTo) {
            return redirect()->route('charges.index')
                ->with('error', 'Veuillez sélectionner une période pour exporter');
        }

        return Excel::download(
            new ChargesExport($dateFrom, $dateTo),
            'charges_' . $dateFrom . '_' . $dateTo . '.xlsx'
        );
    }
}
