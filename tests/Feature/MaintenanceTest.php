<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Maintenance;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Maintenance', 'route' => 'maintenances.index']);
        $this->user->pages()->attach($page->id);

        $this->equipment = Equipment::create([
            'name' => 'Dell PowerEdge R740',
            'serial_number' => 'PE-740-001',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
        ]);
    }

    public function test_can_list_maintenances(): void
    {
        Maintenance::create([
            'equipment_id' => $this->equipment->id,
            'title' => 'Nettoyage annuel serveur',
            'type' => 'Preventive',
            'status' => 'Scheduled',
            'scheduled_at' => now()->addDays(5),
            'cost' => 1500.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('maintenances.index'));

        $response->assertStatus(200);
        $response->assertSee('Nettoyage annuel serveur');
        $response->assertSee('Dell PowerEdge R740');
    }

    public function test_can_create_maintenance_and_updates_equipment_status(): void
    {
        $response = $this->actingAs($this->user)->post(route('maintenances.store'), [
            'equipment_id' => $this->equipment->id,
            'title' => 'Changement disque SSD defectueux',
            'type' => 'Corrective',
            'status' => 'In Progress',
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'cost' => 2500.00,
            'provider' => 'SAV Dell',
        ]);

        $response->assertRedirect(route('maintenances.index'));

        $this->assertDatabaseHas('maintenances', [
            'equipment_id' => $this->equipment->id,
            'title' => 'Changement disque SSD defectueux',
            'status' => 'In Progress',
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $this->equipment->id,
            'status' => 'In Maintenance',
        ]);
    }

    public function test_can_update_maintenance_to_completed_and_restores_equipment_status(): void
    {
        $maintenance = Maintenance::create([
            'equipment_id' => $this->equipment->id,
            'title' => 'Changement RAM',
            'type' => 'Upgrade',
            'status' => 'In Progress',
            'scheduled_at' => now(),
            'cost' => 1000.00,
        ]);
        $this->equipment->update(['status' => 'In Maintenance']);

        $response = $this->actingAs($this->user)->put(route('maintenances.update', $maintenance), [
            'title' => 'Changement RAM',
            'type' => 'Upgrade',
            'status' => 'Completed',
            'scheduled_at' => now()->format('Y-m-d H:i:s'),
            'completed_at' => now()->format('Y-m-d H:i:s'),
            'cost' => 1000.00,
        ]);

        $response->assertRedirect(route('maintenances.index'));

        $this->assertDatabaseHas('maintenances', [
            'id' => $maintenance->id,
            'status' => 'Completed',
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $this->equipment->id,
            'status' => 'Available',
        ]);
    }
}
