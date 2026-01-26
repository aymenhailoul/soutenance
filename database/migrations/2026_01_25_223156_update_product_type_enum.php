<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand Enum to include 'Produit'
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('Product', 'Service', 'Produit') NOT NULL");

        // 2. Migrate Data
        DB::table('products')->where('type', 'Product')->update(['type' => 'Produit']);

        // 3. Restrict Enum to remove 'Product'
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('Produit', 'Service') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('Produit', 'Service', 'Product') NOT NULL");
        DB::table('products')->where('type', 'Produit')->update(['type' => 'Product']);
        DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('Product', 'Service') NOT NULL");
    }
};
