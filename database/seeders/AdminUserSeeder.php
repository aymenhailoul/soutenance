<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing users
        User::query()->delete();
        $this->command->info('All existing users deleted.');

        // Create admin user
        $admin = User::create([
            'name' => 'admin',
            'password' => 'admin123', // Will be hashed automatically
        ]);

        // Give admin access to all pages
        $allPageIds = Page::pluck('id')->toArray();
        $admin->pages()->sync($allPageIds);

        $this->command->info('Admin user created with access to all pages!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
    }
}
