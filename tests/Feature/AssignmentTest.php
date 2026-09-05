<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Equipment $equipment;
    protected Client $client;
    protected Site $site;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $page = Page::create(['name' => 'Affectations', 'route' => 'assignments.index']);
        $this->user->pages()->attach($page->id);

        $this->client = Client::create(['name' => 'OCP Group']);
        $this->site = Site::create(['name' => 'Mine Khouribga', 'client_id' => $this->client->id]);
        $this->employee = Employee::create([
            'name' => 'Karim Alami',
            'cin' => 'AB123456',
            'salary' => 8000.00,
            'joined_at' => '2024-01-01',
        ]);

        $this->equipment = Equipment::create([
            'name' => 'ThinkPad T14',
            'serial_number' => 'TP-990011',
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
        ]);
    }

    public function test_can_list_assignments(): void
    {
        EquipmentAssignment::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'site_id' => $this->site->id,
            'employee_id' => $this->employee->id,
            'assigned_at' => now(),
            'status' => 'Active',
        ]);

        $response = $this->actingAs($this->user)->get(route('assignments.index'));

        $response->assertStatus(200);
        $response->assertSee('ThinkPad T14');
        $response->assertSee('Karim Alami');
    }

    public function test_can_create_assignment_and_updates_equipment_status(): void
    {
        $response = $this->actingAs($this->user)->post(route('assignments.store'), [
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'site_id' => $this->site->id,
            'employee_id' => $this->employee->id,
            'assigned_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Affectation poste de travail IT',
        ]);

        $response->assertRedirect(route('assignments.index'));

        $this->assertDatabaseHas('equipment_assignments', [
            'equipment_id' => $this->equipment->id,
            'employee_id' => $this->employee->id,
            'status' => 'Active',
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $this->equipment->id,
            'status' => 'Assigned',
            'site_id' => $this->site->id,
        ]);
    }

    public function test_can_return_assignment_and_restores_equipment_status(): void
    {
        $assignment = EquipmentAssignment::create([
            'equipment_id' => $this->equipment->id,
            'client_id' => $this->client->id,
            'site_id' => $this->site->id,
            'employee_id' => $this->employee->id,
            'assigned_at' => now()->subDays(10),
            'status' => 'Active',
        ]);
        $this->equipment->update(['status' => 'Assigned']);

        $response = $this->actingAs($this->user)->post(route('assignments.return', $assignment), [
            'returned_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Matériel retourné en bon état.',
        ]);

        $response->assertRedirect(route('assignments.index'));

        $this->assertDatabaseHas('equipment_assignments', [
            'id' => $assignment->id,
            'status' => 'Returned',
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $this->equipment->id,
            'status' => 'Available',
        ]);
    }
}
