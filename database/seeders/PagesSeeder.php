<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['name' => 'Dashboard', 'route' => 'dashboard'],
            ['name' => 'Equipment', 'route' => 'equipment.index'],
            ['name' => 'Categories', 'route' => 'categories.index'],
            ['name' => 'Assignments', 'route' => 'assignments.index'],
            ['name' => 'Maintenances', 'route' => 'maintenances.index'],
            ['name' => 'Clients', 'route' => 'clients.index'],
            ['name' => 'Sites', 'route' => 'sites.index'],
            ['name' => 'Stock Movements', 'route' => 'stock.index'],
            ['name' => 'Reports', 'route' => 'reports.index'],
            ['name' => 'Backups', 'route' => 'backups.index'],
            ['name' => 'Users Management', 'route' => 'users.index'],
            ['name' => 'Employees', 'route' => 'employees.index'],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['route' => $page['route']],
                ['name' => $page['name']]
            );
        }

        $this->command->info('Pages seeded successfully!');
    }
}
