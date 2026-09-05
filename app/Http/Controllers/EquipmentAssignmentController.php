<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Site;
use Illuminate\Http\Request;

class EquipmentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = EquipmentAssignment::with(['equipment', 'client', 'site', 'employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->input('equipment_id'));
        }

        $assignments = $query->orderBy('assigned_at', 'desc')->paginate(15)->withQueryString();
        $equipments = Equipment::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $sites = Site::orderBy('name')->get();
        $employees = Employee::orderBy('name')->get();

        return view('assignments.index', compact('assignments', 'equipments', 'clients', 'sites', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'integer', 'exists:equipment,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'site_id' => ['nullable', 'integer', 'exists:sites,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'assigned_at' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment = EquipmentAssignment::create([
            'equipment_id' => $validated['equipment_id'],
            'client_id' => $validated['client_id'] ?? null,
            'site_id' => $validated['site_id'] ?? null,
            'employee_id' => $validated['employee_id'] ?? null,
            'assigned_at' => $validated['assigned_at'],
            'status' => 'Active',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update equipment status and site
        $equipment = Equipment::find($validated['equipment_id']);
        if ($equipment) {
            $equipment->update([
                'status' => 'Assigned',
                'site_id' => $validated['site_id'] ?? $equipment->site_id,
            ]);
        }

        return redirect()->route('assignments.index')->with('success', 'Affectation d\'équipement enregistrée avec succès.');
    }

    public function returnEquipment(Request $request, EquipmentAssignment $assignment)
    {
        $assignedDate = $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d') : 'today';
        $validated = $request->validate([
            'returned_at' => ['required', 'date', 'after_or_equal:' . $assignedDate],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignment->update([
            'returned_at' => $validated['returned_at'],
            'status' => 'Returned',
            'notes' => $assignment->notes . ($validated['notes'] ? "\n[Retour]: " . $validated['notes'] : ''),
        ]);

        // If no active assignments remain for this equipment, set status to Available
        $activeCount = EquipmentAssignment::where('equipment_id', $assignment->equipment_id)
            ->where('status', 'Active')
            ->count();

        if ($activeCount === 0) {
            $equipment = Equipment::find($assignment->equipment_id);
            if ($equipment && $equipment->status === 'Assigned') {
                $equipment->update(['status' => 'Available']);
            }
        }

        return redirect()->route('assignments.index')->with('success', 'Retour d\'équipement enregistré avec succès.');
    }
}
