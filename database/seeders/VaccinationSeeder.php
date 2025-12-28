<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vaccination;
use App\Models\Animal;

class VaccinationSeeder extends Seeder
{
    public function run(): void
    {
        $veterinarians = ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Davis'];

        $vaccines = [
            'FMD (Foot and Mouth Disease)',
            'Brucellosis',
            'Blackleg',
            'IBR (Infectious Bovine Rhinotracheitis)',
            'BVD (Bovine Viral Diarrhea)',
            'Leptospirosis',
            'Anthrax',
            'Clostridial Diseases'
        ];

        // Get all animal IDs
        $animalIds = Animal::pluck('id')->toArray();

        if (empty($animalIds)) {
            $this->command->warn('No animals found. Please seed animals first.');
            return;
        }

        // Clear existing vaccinations if any
        Vaccination::truncate();

        // Vaccinate random animals from the last 3 months
        for ($i = 1; $i <= 60; $i++) {
            $daysAgo = rand(1, 90);
            $dateAdministered = now()->subDays($daysAgo);
            $nextDueDate = $dateAdministered->copy()->addMonths(rand(3, 12));

            Vaccination::create([
                'animal_id' => $animalIds[array_rand($animalIds)],
                'vaccine_name' => $vaccines[array_rand($vaccines)],
                'date_administered' => $dateAdministered,
                'next_due_date' => $nextDueDate,
                'batch_number' => 'BATCH-' . rand(1000, 9999),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'notes' => rand(0, 1) ? 'Routine vaccination completed successfully' : null
            ]);
        }

        // Add some due/overdue vaccinations for testing
        for ($i = 1; $i <= 10; $i++) {
            $daysAgo = rand(150, 365);
            $dateAdministered = now()->subDays($daysAgo);
            $nextDueDate = now()->addDays(rand(-5, 7)); // Some overdue, some due soon

            Vaccination::create([
                'animal_id' => $animalIds[array_rand($animalIds)],
                'vaccine_name' => $vaccines[array_rand($vaccines)],
                'date_administered' => $dateAdministered,
                'next_due_date' => $nextDueDate,
                'batch_number' => 'BATCH-' . rand(1000, 9999),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'notes' => 'Follow-up vaccination required'
            ]);
        }

        $this->command->info('Vaccination records seeded successfully!');
    }
}

