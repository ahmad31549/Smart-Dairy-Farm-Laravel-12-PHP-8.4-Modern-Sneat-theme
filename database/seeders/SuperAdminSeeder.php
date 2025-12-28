<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dairyfarm.com'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => 'admin123',
                'phone' => '+92 300 1111111',
                'role' => 'super_admin',
                'farm_name' => 'Smart Dairy Farm',
                'bio' => 'System administrator with full access to all modules.',
                'address' => '123 Admin Street',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'status' => 'active',
            ]
        );
    }
}
