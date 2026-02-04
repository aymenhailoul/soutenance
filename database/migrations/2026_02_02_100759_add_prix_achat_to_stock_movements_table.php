<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('prix_achat', 10, 2)->nullable()->after('quantity');
        });

        // Backfill existing entries with the current product price
        DB::statement('
            UPDATE stock_movements sm
            JOIN products p ON sm.product_id = p.id
            SET sm.prix_achat = p.prix_achat
            WHERE sm.movement = "Entrée"
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('prix_achat');
        });
    }
};
