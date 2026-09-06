<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Site;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class EquipmentAssignmentController extends Controller
{
    protected AssignmentService $assignmentService;

    public function __construct(AssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;
    }

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
        $equipments = Equipment::where('is_consumable', false)
            ->where('status', 'Available')
            ->orderBy('name')
            ->get();
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
            'assigned_at' => ['required', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->assignmentService->createAssignment($validated);

        return redirect()->route('assignments.index')->with('success', 'Affectation d\'équipement enregistrée avec succès.');
    }

    public function returnEquipment(Request $request, EquipmentAssignment $assignment)
    {
        $assignedDate = $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : 'now';
        $validated = $request->validate([
            'returned_at' => ['required', 'date', 'after_or_equal:' . $assignedDate],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->assignmentService->returnAssignment($assignment, $validated);

        return redirect()->route('assignments.index')->with('success', 'Retour d\'équipement enregistré avec succès.');
    }
}
