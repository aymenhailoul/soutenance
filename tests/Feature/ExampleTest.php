<?php

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

/**
 * Seed pages and grant all permissions to the given admin.
 */
function seedPagesForAdmin(User $admin): void
{
    $routes = [
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
    foreach ($routes as $route) {
        Page::firstOrCreate(['route' => $route], ['name' => $route]);
    }
    $admin->pages()->syncWithoutDetaching(Page::all()->pluck('id')->toArray());
}

it('redirects unauthenticated users to login', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});

it('renders dashboard for authenticated admin', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    seedPagesForAdmin($admin);

    $response = $this->actingAs($admin)->get('/');

    $response->assertStatus(200);
});
