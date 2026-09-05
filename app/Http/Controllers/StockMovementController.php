<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['equipment', 'user']);

        if ($request->filled('movement')) {
            $query->where('movement', $request->input('movement'));
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $equipments = Equipment::orderBy('name')->get();

        return view('stock_movements.index', compact('movements', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'movement' => ['required', 'string', 'in:Entrée,Sortie,Transfert'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'prix_achat' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $prix_achat = $validated['prix_achat'] ?? 0;
        $montant = $prix_achat * $validated['quantity'];

        StockMovement::create([
            'equipment_id' => $validated['equipment_id'],
            'movement' => $validated['movement'],
            'quantity' => $validated['quantity'],
            'prix_achat' => $prix_achat,
            'montant' => $montant,
            'user_id' => auth()->user()?->id,
            'comment' => $validated['comment'] ?? null,
        ]);

        // Update equipment overall quantity if not consumable
        $equipment = Equipment::find($validated['equipment_id']);
        if ($equipment && !$equipment->is_consumable) {
            if ($validated['movement'] === 'Entrée') {
                $equipment->increment('quantity', $validated['quantity']);
            } elseif ($validated['movement'] === 'Sortie') {
                $equipment->decrement('quantity', min($equipment->quantity, $validated['quantity']));
            }
        }

        return redirect()->route('stock.index')->with('success', 'Mouvement de stock enregistré avec succès.');
    }
}
