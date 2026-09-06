<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Équipements', 'route' => 'equipment.index']);
        $this->user->pages()->attach($page->id);

        $this->category = Category::create(['name' => 'Serveurs']);
    }

    public function test_can_list_equipment(): void
    {
        Equipment::create([
            'name' => 'Dell PowerEdge R740',
            'category_id' => $this->category->id,
            'serial_number' => 'SN-100200',
            'asset_tag' => 'ISS-SRV-001',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('equipment.index'));

        $response->assertStatus(200);
        $response->assertSee('Dell PowerEdge R740');
    }

    public function test_can_create_equipment(): void
    {
        $response = $this->actingAs($this->user)->post(route('equipment.store'), [
            'name' => 'Cisco Catalyst 9300',
            'category_id' => $this->category->id,
            'brand' => 'Cisco',
            'model' => 'C9300-48U',
            'serial_number' => 'FCW2345L0AB',
            'asset_tag' => 'ISS-SW-001',
            'purchase_date' => '2026-01-15',
            'purchase_price' => 15000.00,
            'status' => 'Available',
            'condition' => 'New',
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('equipment', [
            'name' => 'Cisco Catalyst 9300',
            'serial_number' => 'FCW2345L0AB',
            'asset_tag' => 'ISS-SW-001',
        ]);

        $equipment = Equipment::where('serial_number', 'FCW2345L0AB')->first();
        $response->assertRedirect(route('equipment.show', $equipment));
    }

    public function test_can_view_equipment_detail(): void
    {
        $equipment = Equipment::create([
            'name' => 'HP EliteBook 840',
            'category_id' => $this->category->id,
            'serial_number' => 'HP-840-001',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('equipment.show', $equipment));

        $response->assertStatus(200);
        $response->assertSee('HP EliteBook 840');
        $response->assertSee('HP-840-001');
    }

    public function test_can_update_equipment(): void
    {
        $equipment = Equipment::create([
            'name' => 'Ancien Équipement',
            'status' => 'Available',
            'condition' => 'Fair',
            'quantity' => 1,
            'is_consumable' => false,
        ]);

        $response = $this->actingAs($this->user)->put(route('equipment.update', $equipment), [
            'name' => 'Équipement Mis à Jour',
            'status' => 'Broken',
            'condition' => 'Good',
        ]);

        $response->assertRedirect(route('equipment.show', $equipment));
        $this->assertDatabaseHas('equipment', [
            'id' => $equipment->id,
            'name' => 'Équipement Mis à Jour',
            'status' => 'Broken',
            'condition' => 'Good',
            'quantity' => 1,
        ]);
    }

    public function test_cannot_set_assigned_or_in_maintenance_status_via_generic_edit(): void
    {
        $equipment = Equipment::create([
            'name' => 'Équipement Test',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
            'is_consumable' => false,
        ]);

        $response = $this->actingAs($this->user)->put(route('equipment.update', $equipment), [
            'name' => 'Équipement Test',
            'status' => 'Assigned',
            'condition' => 'Good',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals('Available', $equipment->fresh()->status);
    }

    public function test_cannot_change_is_consumable_when_operational_history_exists(): void
    {
        $equipment = Equipment::create([
            'name' => 'Imprimante HP',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
            'is_consumable' => false,
        ]);

        $employee = \App\Models\Employee::create([
            'name' => 'Jean Dupont',
            'cin' => 'AB123456',
            'salary' => 5000.00,
            'joined_at' => '2025-01-01',
        ]);

        // Create assignment history
        \App\Models\EquipmentAssignment::create([
            'equipment_id' => $equipment->id,
            'employee_id' => $employee->id,
            'assigned_at' => now(),
            'status' => 'Returned',
        ]);

        $response = $this->actingAs($this->user)->put(route('equipment.update', $equipment), [
            'name' => 'Imprimante HP',
            'condition' => 'Good',
            'is_consumable' => true,
        ]);

        $response->assertSessionHasErrors('is_consumable');
        $this->assertFalse((bool) $equipment->fresh()->is_consumable);
    }

    public function test_can_delete_equipment(): void
    {
        $equipment = Equipment::create([
            'name' => 'À supprimer',
            'status' => 'Retired',
            'condition' => 'Damaged',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->delete(route('equipment.destroy', $equipment));

        $response->assertRedirect(route('equipment.index'));
        $this->assertSoftDeleted('equipment', ['id' => $equipment->id]);
    }
}
