<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update the admin user
        $admin = User::updateOrCreate(
            ['name' => 'aymen'],
            [
                'password' => 'gokussj2', // hashed automatically via User model cast
                'role' => 'Admin',
            ]
        );

        // Give admin access to ALL pages
        $allPageIds = Page::pluck('id')->toArray();
        $admin->pages()->sync($allPageIds);

        $this->command->info('Admin user ready!');
        $this->command->info('Username : aymen');
        $this->command->info('Password : gokussj2');
        $this->command->info('Role     : Admin');
    }
}
