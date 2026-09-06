<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            if (!Schema::hasColumn('equipment', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'source_site_id')) {
                $table->foreignId('source_site_id')->nullable()->after('quantity')->constrained('sites')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_movements', 'destination_site_id')) {
                $table->foreignId('destination_site_id')->nullable()->after('source_site_id')->constrained('sites')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'destination_site_id')) {
                $table->dropForeign(['destination_site_id']);
                $table->dropColumn('destination_site_id');
            }
            if (Schema::hasColumn('stock_movements', 'source_site_id')) {
                $table->dropForeign(['source_site_id']);
                $table->dropColumn('source_site_id');
            }
        });

        Schema::table('equipment', function (Blueprint $table) {
            if (Schema::hasColumn('equipment', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
