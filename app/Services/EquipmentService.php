<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;

class EquipmentService
{
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

            // Check if there is an active maintenance (In Progress or Scheduled)
            $hasActiveMaintenance = Maintenance::where('equipment_id', $lockedEquipment->id)
                ->whereIn('status', ['In Progress', 'Scheduled'])
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

            // Preserve terminal statuses if no active maintenance or assignment exists
            if (in_array($lockedEquipment->status, ['Broken', 'Retired', 'Lost'])) {
                return $lockedEquipment->status;
            }

            $lockedEquipment->update(['status' => 'Available']);
            return 'Available';
        });
    }
}
