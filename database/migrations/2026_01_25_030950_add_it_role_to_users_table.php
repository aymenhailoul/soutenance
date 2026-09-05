<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The remove_role_from_users_table migration already dropped any previous role column.
        // This migration adds the IT-specific role enum.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['Admin', 'Manager', 'Technician', 'Viewer'])->default('Viewer')->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
