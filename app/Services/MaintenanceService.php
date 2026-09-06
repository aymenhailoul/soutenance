<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceService
{
    protected EquipmentService $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Store a new maintenance record and synchronize equipment state.
     *
     * @throws ValidationException
     */
    public function createMaintenance(array $data): Maintenance
    {
        return DB::transaction(function () use ($data) {
            /** @var Equipment $equipment */
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);

            if ($equipment->is_consumable) {
                throw ValidationException::withMessages([
                    'equipment_id' => 'Les consommables ne peuvent pas être placés en maintenance.',
                ]);
            }

            if (in_array($equipment->status, ['Broken', 'Retired', 'Lost']) && !in_array($data['status'], ['Cancelled'])) {
                // Allowed if corrective maintenance
            }

            if ($data['status'] === 'Completed' && empty($data['completed_at'])) {
                $data['completed_at'] = now()->format('Y-m-d H:i:s');
            }

            $maintenance = Maintenance::create($data);

            $this->equipmentService->recalculateStatus($equipment);

            return $maintenance;
        });
    }

    /**
     * Update an existing maintenance record and synchronize equipment state.
     *
     * @throws ValidationException
     */
    public function updateMaintenance(Maintenance $maintenance, array $data): Maintenance
    {
        return DB::transaction(function () use ($maintenance, $data) {
            $maintenance = Maintenance::lockForUpdate()->findOrFail($maintenance->id);

            if ($data['status'] === 'Completed' && empty($data['completed_at']) && empty($maintenance->completed_at)) {
                $data['completed_at'] = now()->format('Y-m-d H:i:s');
            }

            $maintenance->update($data);

            $equipment = Equipment::lockForUpdate()->find($maintenance->equipment_id);
            if ($equipment) {
                $this->equipmentService->recalculateStatus($equipment);
            }

            return $maintenance;
        });
    }
}
