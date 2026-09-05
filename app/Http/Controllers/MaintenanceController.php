<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::with('equipment');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        $maintenances = $query->orderBy('scheduled_at', 'desc')->paginate(15)->withQueryString();
        $equipments = Equipment::orderBy('name')->get();

        return view('maintenances.index', compact('maintenances', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', 'string', 'in:Preventive,Corrective,Upgrade'],
            'provider' => ['nullable', 'string', 'max:150'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', 'string', 'in:Scheduled,In Progress,Completed,Cancelled'],
            'scheduled_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:scheduled_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $maintenance = Maintenance::create($validated);

        // If maintenance is set to 'In Progress', set equipment status to 'In Maintenance'
        if ($validated['status'] === 'In Progress') {
            Equipment::where('id', $validated['equipment_id'])->update(['status' => 'In Maintenance']);
        } elseif ($validated['status'] === 'Completed') {
            Equipment::where('id', $validated['equipment_id'])->update(['status' => 'Available']);
        }

        return redirect()->route('maintenances.index')->with('success', 'Maintenance enregistrée avec succès.');
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', 'string', 'in:Preventive,Corrective,Upgrade'],
            'provider' => ['nullable', 'string', 'max:150'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', 'string', 'in:Scheduled,In Progress,Completed,Cancelled'],
            'scheduled_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:scheduled_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $maintenance->update($validated);

        // Update equipment status based on maintenance state
        $equipment = $maintenance->equipment;
        if ($equipment) {
            if ($validated['status'] === 'In Progress') {
                $equipment->update(['status' => 'In Maintenance']);
            } elseif ($validated['status'] === 'Completed') {
                $activeMaintenances = Maintenance::where('equipment_id', $equipment->id)
                    ->whereIn('status', ['In Progress'])
                    ->where('id', '!=', $maintenance->id)
                    ->count();
                if ($activeMaintenances === 0 && $equipment->status === 'In Maintenance') {
                    $equipment->update(['status' => 'Available']);
                }
            }
        }

        return redirect()->route('maintenances.index')->with('success', 'Maintenance mise à jour avec succès.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('maintenances.index')->with('success', 'Maintenance supprimée avec succès.');
    }
}
