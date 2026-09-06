<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Maintenance;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Equipment $equipment;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Maintenances', 'route' => 'maintenances.index']);
        $this->user->pages()->attach($page->id);

        $this->employee = Employee::create([
            'name' => 'Youssef Mansouri',
            'cin' => 'CD987654',
            'salary' => 9500.00,
            'joined_at' => '2023-05-10',
        ]);

        $this->equipment = Equipment::create([
            'name' => 'Dell XPS 15',
            'serial_number' => 'DXPS-5500',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
            'is_consumable' => false,
        ]);
    }

    public function test_starting_maintenance_sets_equipment_status_to_in_maintenance(): void
    {
        $response = $this->actingAs($this->user)->post(route('maintenances.store'), [
            'equipment_id' => $this->equipment->id,
            'title' => 'Remplacement Clavier',
            'type' => 'Corrective',
            'provider' => 'Dell Repair Center',
            'cost' => 450.00,
            'status' => 'In Progress',
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('maintenances.index'));
        $this->assertEquals('In Maintenance', $this->equipment->fresh()->status);
    }

    public function test_completing_maintenance_restores_assigned_status_if_active_assignment_exists(): void
    {
        // Equipment assigned to employee
        EquipmentAssignment::create([
            'equipment_id' => $this->equipment->id,
            'employee_id' => $this->employee->id,
            'assigned_at' => now()->subMonth(),
            'status' => 'Active',
        ]);
        $this->equipment->update(['status' => 'Assigned']);

        // Register maintenance
        $maintenance = Maintenance::create([
            'equipment_id' => $this->equipment->id,
            'title' => 'Nettoyage ventilateur',
            'type' => 'Preventive',
            'status' => 'In Progress',
            'scheduled_at' => now(),
        ]);
        $this->equipment->update(['status' => 'In Maintenance']);

        // Complete maintenance
        $response = $this->actingAs($this->user)->put(route('maintenances.update', $maintenance), [
            'title' => 'Nettoyage ventilateur',
            'type' => 'Preventive',
            'status' => 'Completed',
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'completed_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('maintenances.index'));
        // Must restore to 'Assigned' because an active assignment still exists!
        $this->assertEquals('Assigned', $this->equipment->fresh()->status);
    }
}
