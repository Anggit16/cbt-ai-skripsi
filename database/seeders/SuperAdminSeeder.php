<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Update atau create super admin
        User::updateOrCreate(
            ['email' => 'admin@cbt.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
            ]
        );

        $this->command->info('Super Admin: admin@cbt.com / admin123');
    }
}