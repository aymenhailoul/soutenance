<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            ['name' => 'Dashboard', 'route' => 'dashboard'],
            ['name' => 'Users Management', 'route' => 'users.index'],
            ['name' => 'Clients', 'route' => 'clients.index'],
            ['name' => 'Stock Management', 'route' => 'stock.index'],
            ['name' => 'Stock Movements', 'route' => 'stock.movements'],
            ['name' => 'Products', 'route' => 'products.index'],
            ['name' => 'Create Product', 'route' => 'products.create'],
            ['name' => 'Facturation', 'route' => 'facturation.index'],
            ['name' => 'Ventes', 'route' => 'ventes.index'],
            ['name' => 'Employees', 'route' => 'employees.index'],
            ['name' => 'Charges', 'route' => 'charges.index'],
            ['name' => 'View Purchase Price', 'route' => 'products.show_cost'],
            ['name' => 'View Total Sales', 'route' => 'dashboard.total_sales'],
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
