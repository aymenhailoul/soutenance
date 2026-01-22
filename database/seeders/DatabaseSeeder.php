<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default super admin user if none exist
        User::firstOrCreate(
            ['name' => 'admin'],
            [
                'password' => 'admin123', // will be hashed automatically by the model cast
                'role' => 'Super User',
            ]
        );
    }
}
