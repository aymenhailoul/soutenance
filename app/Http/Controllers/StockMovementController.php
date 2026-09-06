<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Site;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['equipment', 'user', 'sourceSite', 'destinationSite']);

        if ($request->filled('movement')) {
            $query->where('movement', $request->input('movement'));
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $equipments = Equipment::orderBy('name')->get();
        $sites = Site::orderBy('name')->get();

        return view('stock_movements.index', compact('movements', 'equipments', 'sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'movement' => ['required', 'string', 'in:Entrée,Sortie,Transfert'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'source_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'destination_site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'prix_achat' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->stockService->recordMovement($validated, auth()->id());

        return redirect()->route('stock.index')->with('success', 'Mouvement de stock enregistré avec succès.');
    }
}
