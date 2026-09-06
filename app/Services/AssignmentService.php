<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentService
{
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Create an equipment assignment with target validation and double-assignment protection.
     *
     * @throws ValidationException
     */
    public function createAssignment(array $data): EquipmentAssignment
    {
        return DB::transaction(function () use ($data) {
            /** @var Equipment $equipment */
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);

            if ($equipment->is_consumable) {
                throw ValidationException::withMessages([
                    'equipment_id' => 'Les consommables ne peuvent pas faire l\'objet d\'affectations individuelles.',
                ]);
            }

            // Check if equipment status allows assignment
            if ($equipment->status !== 'Available') {
                throw ValidationException::withMessages([
                    'equipment_id' => "Cet équipement n'est pas disponible pour affectation (Statut actuel: {$equipment->status}).",
                ]);
            }

            // Check for existing active assignment
            $activeAssignmentExists = EquipmentAssignment::where('equipment_id', $equipment->id)
                ->where('status', 'Active')
                ->lockForUpdate()
                ->exists();

            if ($activeAssignmentExists) {
                throw ValidationException::withMessages([
                    'equipment_id' => 'Cet équipement est déjà affecté à un bénéficiaire actif.',
                ]);
            }

            // Enforce single assignment target rule (exactly one of employee_id, client_id, site_id)
            $targets = array_filter([
                'employee_id' => $data['employee_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'site_id' => $data['site_id'] ?? null,
            ]);

            if (count($targets) !== 1) {
                throw ValidationException::withMessages([
                    'target' => 'L\'affectation doit cibler exactement un bénéficiaire (Employé, Client ou Site).',
                ]);
            }

            // Clean data so non-selected targets are null
            $assignment = EquipmentAssignment::create([
                'equipment_id' => $equipment->id,
                'employee_id' => $data['employee_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'site_id' => $data['site_id'] ?? null,
                'assigned_at' => $data['assigned_at'],
                'status' => 'Active',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update equipment status & site if site assigned
            $updateData = ['status' => 'Assigned'];
            if (!empty($data['site_id'])) {
                $updateData['site_id'] = $data['site_id'];
            }
            $equipment->update($updateData);

            return $assignment;
        });
    }

    /**
     * Return an equipment assignment and update equipment status cleanly.
     *
     * @throws ValidationException
     */
    public function returnAssignment(EquipmentAssignment $assignment, array $data): EquipmentAssignment
    {
        return DB::transaction(function () use ($assignment, $data) {
            $assignment = EquipmentAssignment::lockForUpdate()->findOrFail($assignment->id);

            if ($assignment->status === 'Returned') {
                throw ValidationException::withMessages([
                    'assignment' => 'Cette affectation a déjà été retournée.',
                ]);
            }

            $assignment->update([
                'returned_at' => $data['returned_at'],
                'status' => 'Returned',
                'notes' => trim(($assignment->notes ?? '') . ($data['notes'] ? "\n[Retour]: " . $data['notes'] : '')),
            ]);

            // Recalculate equipment status via EquipmentService
            $equipment = Equipment::lockForUpdate()->find($assignment->equipment_id);
            if ($equipment) {
                $this->equipmentService->recalculateStatus($equipment);
            }

            return $assignment;
        });
    }
}
