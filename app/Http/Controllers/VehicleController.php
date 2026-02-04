<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Client;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Store a newly created vehicle.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'plaque' => 'required|string|max:255|unique:vehicles,plaque',
            'marque' => 'required|string|max:255',
            'modele' => 'nullable|string|max:255',
            'annee' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'kilometrage' => 'nullable|integer|min:0',
            'carburant' => 'required|in:essence,diesel,hybride,electrique',
            'couleur' => 'nullable|string|max:255',
        ]);

        Vehicle::create($validated);

        return redirect()->back()->with('success', 'Véhicule ajouté avec succès.');
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->back()->with('success', 'Véhicule supprimé avec succès.');
    }

    /**
     * Display the service history for a vehicle.
     */
    public function history(Vehicle $vehicle)
    {
        $vehicle->load('client');
        
        $invoices = $vehicle->invoices()
            ->where('status', 'Finalized')
            ->orderBy('invoice_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->with('items.product')
            ->paginate(5);

        return view('vehicles.history', compact('vehicle', 'invoices'));
    }

    /**
     * Search vehicles by client for invoice creation.
     */
    public function search(Request $request)
    {
        $clientId = $request->input('client_id');
        
        if (!$clientId) {
            return response()->json([]);
        }

        $vehicles = Vehicle::where('client_id', $clientId)
            ->orderBy('marque')
            ->get(['id', 'plaque', 'marque', 'modele', 'kilometrage']);

        return response()->json($vehicles);
    }
}
