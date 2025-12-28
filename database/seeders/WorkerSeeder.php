<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Farm Worker
        User::updateOrCreate(
            ['email' => 'worker@dairyfarm.com'],
            [
                'name' => 'Farm Worker',
                'username' => 'worker',
                'password' => 'worker123',
                'phone' => '+92 300 9876543',
                'role' => 'farm_worker',
                'farm_name' => 'Smart Dairy Farm',
                'bio' => 'Dedicated farm worker responsible for daily operations.',
                'address' => '456 Worker Street',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'status' => 'active',
            ]
        );

        // Create another worker for testing
        User::updateOrCreate(
            ['email' => 'worker2@dairyfarm.com'],
            [
                'name' => 'Ali Khan',
                'username' => 'alikhan',
                'password' => 'worker123',
                'phone' => '+92 300 1112222',
                'role' => 'farm_worker',
                'farm_name' => 'Smart Dairy Farm',
                'bio' => 'Experienced farm worker specializing in milk production.',
                'address' => '789 Farm Lane',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'status' => 'active',
            ]
        );
    }
}
