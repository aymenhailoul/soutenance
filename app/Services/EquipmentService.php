<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EquipmentService
{
    /**
     * Update equipment fields safely enforcing status authority, quantity authority, and history locks.
     *
     * @throws ValidationException
     */
    public function updateEquipment(Equipment $equipment, array $data): Equipment
    {
        return DB::transaction(function () use ($equipment, $data) {
            $lockedEquipment = Equipment::lockForUpdate()->findOrFail($equipment->id);

            // 1. Check is_consumable change against operational history
            if (isset($data['is_consumable'])) {
                $newIsConsumable = (bool) $data['is_consumable'];
                if ($newIsConsumable !== (bool) $lockedEquipment->is_consumable) {
                    $hasHistory = $lockedEquipment->assignments()->exists()
                        || $lockedEquipment->maintenances()->exists()
                        || $lockedEquipment->stockMovements()->exists();

                    if ($hasHistory) {
                        throw ValidationException::withMessages([
                            'is_consumable' => 'Impossible de modifier le type (consommable / non-consommable) car un historique d\'opérations existe.',
                        ]);
                    }
                }
                $data['is_consumable'] = $newIsConsumable;
            } else {
                $data['is_consumable'] = $lockedEquipment->is_consumable;
            }

            // 2. Quantity authority: generic edit MUST NOT alter stock quantity
            unset($data['quantity']);
            if (!$data['is_consumable']) {
                $data['quantity'] = 1;
            } else {
                $data['quantity'] = $lockedEquipment->quantity;
            }

            // 3. Status authority: validate lifecycle transition if status is being modified
            if (isset($data['status'])) {
                $this->validateStatusTransition($lockedEquipment, $data['status']);
            } else {
                unset($data['status']);
            }

            $lockedEquipment->update($data);

            // Recalculate status to maintain consistency
            $this->recalculateStatus($lockedEquipment);

            return $lockedEquipment->fresh();
        });
    }

    /**
     * Validate whether an equipment status transition is permitted server-side.
     *
     * @throws ValidationException
     */
    public function validateStatusTransition(Equipment $equipment, string $newStatus): void
    {
        $currentStatus = $equipment->status;

        if ($currentStatus === $newStatus) {
            return;
        }

        // Active lifecycle locks
        $hasActiveAssignment = EquipmentAssignment::where('equipment_id', $equipment->id)
            ->where('status', 'Active')
            ->exists();

        if ($hasActiveAssignment && $currentStatus === 'Assigned') {
            throw ValidationException::withMessages([
                'status' => 'Le statut ne peut pas être modifié manuellement car cet équipement a une affectation active.',
            ]);
        }

        $hasActiveMaintenance = Maintenance::where('equipment_id', $equipment->id)
            ->whereIn('status', ['In Progress'])
            ->exists();

        if ($hasActiveMaintenance && $currentStatus === 'In Maintenance') {
            throw ValidationException::withMessages([
                'status' => 'Le statut ne peut pas être modifié manuellement car cet équipement est actuellement en maintenance.',
            ]);
        }

        // Target status rules for generic updates
        if (in_array($newStatus, ['Assigned', 'In Maintenance'])) {
            throw ValidationException::withMessages([
                'status' => "Le statut '{$newStatus}' ne peut pas être défini directement via l'édition générique. Utilisez le module approprié.",
            ]);
        }

        $allowedTransitions = ['Available', 'Broken', 'Retired', 'Lost'];
        if (!in_array($newStatus, $allowedTransitions)) {
            throw ValidationException::withMessages([
                'status' => "Transition de statut non autorisée ({$currentStatus} -> {$newStatus}).",
            ]);
        }
    }

    /**
     * Recalculate and update an equipment's status based on active maintenance and assignment records.
     */
    public function recalculateStatus(Equipment $equipment): string
    {
        return DB::transaction(function () use ($equipment) {
            $lockedEquipment = Equipment::lockForUpdate()->find($equipment->id);
            if (!$lockedEquipment) {
                return 'Retired';
            }

            // Check if there is an active maintenance currently In Progress
            $hasActiveMaintenance = Maintenance::where('equipment_id', $lockedEquipment->id)
                ->where('status', 'In Progress')
                ->exists();

            if ($hasActiveMaintenance) {
                $lockedEquipment->update(['status' => 'In Maintenance']);
                return 'In Maintenance';
            }

            // Check if there is an active assignment
            $hasActiveAssignment = EquipmentAssignment::where('equipment_id', $lockedEquipment->id)
                ->where('status', 'Active')
                ->exists();

            if ($hasActiveAssignment) {
                $lockedEquipment->update(['status' => 'Assigned']);
                return 'Assigned';
            }

            // Preserve condition/terminal statuses if no active maintenance or assignment exists
            if (in_array($lockedEquipment->status, ['Broken', 'Retired', 'Lost'])) {
                return $lockedEquipment->status;
            }

            $lockedEquipment->update(['status' => 'Available']);
            return 'Available';
        });
    }
}
