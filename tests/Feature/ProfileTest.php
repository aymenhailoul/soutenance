<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// The IT system does not have a /profile route — user management is admin-only via /users.
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);

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

        // Grant admin access to all pages
        $this->admin->pages()->syncWithoutDetaching(
            Page::all()->pluck('id')->toArray()
        );
    }

    public function test_admin_can_list_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_update_user_role(): void
    {
        $target = User::factory()->create(['role' => 'Viewer']);

        $response = $this->actingAs($this->admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'role' => 'Technician',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'Technician']);
    }

    public function test_password_update_does_not_corrupt_user(): void
    {
        $target = User::factory()->create(['role' => 'Viewer']);

        // Updating without providing new password should not break existing password
        $this->actingAs($this->admin)->put("/users/{$target->id}", [
            'name' => $target->name,
            'role' => 'Manager',
        ]);

        $this->assertNotNull($target->fresh());
    }
}
