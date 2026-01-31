<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::firstOrCreate(
            ['serial_code' => '001'],
            [
                'name' => 'Laptop Dell',
                'type' => 'Produit',
                'prix_achat' => 6500.00,
                'prix_vente' => 7800.00,
            ]
        );

        Product::firstOrCreate(
            ['serial_code' => '003'],
            [
                'name' => 'Website Development',
                'type' => 'Service',
                'prix_achat' => null,
                'prix_vente' => 3000.00,
            ]
        );

        Product::firstOrCreate(
            ['serial_code' => '002'],
            [
                'name' => 'Wireless Mouse',
                'type' => 'Produit',
                'prix_achat' => 80.00,
                'prix_vente' => 150.00,
            ]
        );
    }
}
