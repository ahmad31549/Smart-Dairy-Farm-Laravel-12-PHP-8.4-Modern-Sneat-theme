<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run()
    {
        // Veterinary Doctor
        User::updateOrCreate(
            ['email' => 'doctor@farm.com'],
            [
                'name' => 'Dr. Smith',
                'username' => 'drsmith',
                'password' => 'password123', // Will be hashed by model cast
                'role' => 'veterinary_doctor',
                'status' => 'active',
                'farm_name' => 'Main Farm'
            ]
        );

        // Farm Worker
        User::updateOrCreate(
            ['email' => 'worker@farm.com'],
            [
                'name' => 'Ali Worker',
                'username' => 'aliworker',
                'password' => 'password123',
                'role' => 'farm_worker',
                'status' => 'active',
                'farm_name' => 'Main Farm'
            ]
        );
    }
}
