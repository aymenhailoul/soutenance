<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);

        // Seed the pages the system uses
        $pageRoutes = [
            'dashboard',
            'equipment.index',
            'categories.index',
            'assignments.index',
            'maintenances.index',
            'clients.index',
            'sites.index',
            'stock.index',
            'reports.index',
            'backups.index',
            'users.index',
            'employees.index',
        ];
        foreach ($pageRoutes as $route) {
            Page::firstOrCreate(['route' => $route], ['name' => $route]);
        }

        // Grant admin access to users.index
        $adminPage = Page::where('route', 'users.index')->first();
        $this->admin->pages()->syncWithoutDetaching([$adminPage->id]);
    }

    public function test_can_create_user_with_admin_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'TestAdmin',
            'password' => 'password123',
            'role' => 'Admin',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'TestAdmin',
            'role' => 'Admin',
        ]);

        $testUser = User::where('name', 'TestAdmin')->first();
        // Admin should have access to users.index
        $this->assertTrue($testUser->hasPageAccess('users.index'));
    }

    public function test_can_create_user_with_viewer_role(): void
    {
        $response = $this->actingAs($this->admin)->post(route('users.store'), [
            'name' => 'ViewerUser',
            'password' => 'password123',
            'role' => 'Viewer',
        ]);

        $response->assertRedirect();

        $viewer = User::where('name', 'ViewerUser')->first();
        $this->assertEquals('Viewer', $viewer->role);
        // Viewer should see dashboard and equipment but NOT users.index
        $this->assertTrue($viewer->hasPageAccess('dashboard'));
        $this->assertFalse($viewer->hasPageAccess('users.index'));
    }

    public function test_role_helper_methods(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $manager = User::factory()->create(['role' => 'Manager']);
        $tech = User::factory()->create(['role' => 'Technician']);
        $viewer = User::factory()->create(['role' => 'Viewer']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($manager->isAdmin());

        $this->assertTrue($manager->isManager());
        $this->assertFalse($tech->isManager());

        $this->assertTrue($tech->isTechnician());
        $this->assertFalse($viewer->isTechnician());

        $this->assertTrue($viewer->isViewer());
    }

    public function test_can_update_user_role(): void
    {
        $user = User::factory()->create(['role' => 'Viewer']);

        $response = $this->actingAs($this->admin)->put(route('users.update', $user), [
            'name' => $user->name,
            'role' => 'Manager',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'Manager',
        ]);
    }
}
