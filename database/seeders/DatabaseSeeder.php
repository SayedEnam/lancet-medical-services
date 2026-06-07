<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(DiagnosticDemoSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => 'admin@lancet.com'],
            ['name' => 'Super Admin', 'password' => 'password', 'status' => 'active']
        );

        $admin->assignRole('Super Admin');
    }
}
