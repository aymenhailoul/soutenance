<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $dashboardPage = Page::create(['name' => 'Dashboard', 'route' => 'dashboard']);
        $reportsPage = Page::create(['name' => 'Rapports & Exports', 'route' => 'reports.index']);

        $this->user->pages()->attach([$dashboardPage->id, $reportsPage->id]);

        $category = Category::create(['name' => 'Serveurs', 'slug' => 'serveurs']);

        Equipment::create([
            'name' => 'Serveur Dell R740',
            'category_id' => $category->id,
            'status' => 'Available',
            'condition' => 'Good',
            'quantity' => 1,
        ]);
    }

    public function test_can_load_dashboard(): void
    {
        \App\Models\EquipmentAssignment::create([
            'equipment_id' => 1,
            'assigned_at' => now(),
            'status' => 'Active',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Système de Gestion du Parc Informatique');
        $response->assertSee('Serveur Dell R740');
    }

    public function test_can_load_reports_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Rapports');
    }

    public function test_can_export_equipment_csv(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.export.equipment'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
