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
            ['name' => 'Edit Product', 'route' => 'products.edit'],
            ['name' => 'Delete Product', 'route' => 'products.destroy'],
            ['name' => 'Export Products', 'route' => 'products.export'],
            ['name' => 'Facturation Services', 'route' => 'facturation.services.index'],
            ['name' => 'Facturation Produits', 'route' => 'facturation.produits.index'],
            ['name' => 'Ventes', 'route' => 'ventes.index'],
            ['name' => 'Annuler Facture', 'route' => 'ventes.cancel'],
            ['name' => 'Créer Retour', 'route' => 'ventes.return'],
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
