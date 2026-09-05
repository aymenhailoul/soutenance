<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * No-op: The role column is now managed by 2026_01_25_030950_add_it_role_to_users_table.php.
     * This migration kept for reference only.
     */
    public function up(): void
    {
        // Intentionally left empty.
        // Role column management was moved to 2026_01_25_030950_add_it_role_to_users_table.php
    }

    public function down(): void
    {
        //
    }
};
