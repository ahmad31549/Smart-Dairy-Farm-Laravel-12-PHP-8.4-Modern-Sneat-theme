<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Veterinary Doctor
        User::updateOrCreate(
            ['email' => 'doctor@dairyfarm.com'],
            [
                'name' => 'Dr. Veterinary',
                'username' => 'doctor',
                'password' => 'doctor123',
                'phone' => '+92 300 5555555',
                'role' => 'veterinary_doctor',
                'farm_name' => 'Smart Dairy Farm',
                'bio' => 'Experienced veterinary doctor specializing in dairy cattle.',
                'address' => '789 Medical Plaza',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'status' => 'active',
            ]
        );

        // Create another doctor for testing
        User::updateOrCreate(
            ['email' => 'doctor2@dairyfarm.com'],
            [
                'name' => 'Dr. Ahmed Hassan',
                'username' => 'drahmed',
                'password' => 'doctor123',
                'phone' => '+92 300 7778888',
                'role' => 'veterinary_doctor',
                'farm_name' => 'Smart Dairy Farm',
                'bio' => 'Veterinary specialist with 15 years of experience.',
                'address' => '321 Clinic Road',
                'city' => 'Lahore',
                'country' => 'Pakistan',
                'status' => 'active',
            ]
        );
    }
}
