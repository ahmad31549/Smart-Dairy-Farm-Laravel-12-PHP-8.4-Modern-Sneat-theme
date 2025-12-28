<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Employee;
use App\Models\AnimalHealthRecord;
use App\Models\MilkProduction;
use App\Models\MilkQualityTest;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Attendance;
use App\Models\Inventory;
use App\Models\Vaccination;

class DairyFarmSeeder extends Seeder
{
    public function run(): void
    {
        // Create Animals
        $breeds = ['holstein', 'jersey', 'guernsey', 'ayrshire'];
        $animalNames = [
            'Bella', 'Daisy', 'Luna', 'Rosie', 'Molly', 'Buttercup', 'Clover', 'Maggie',
            'Bessie', 'Elsie', 'Pearl', 'Ruby', 'Stella', 'Willow', 'Honey', 'Cinnamon',
            'Cookie', 'Ginger', 'Maple', 'Brownie', 'Hazel', 'Cocoa', 'Caramel', 'Nutmeg'
        ];

        // Create 245 animals (208 healthy + 29 treatment + 8 critical)
        for ($i = 1; $i <= 245; $i++) {
            $birthYear = rand(2018, 2023);
            $birthMonth = rand(1, 12);
            $birthDay = rand(1, 28);

            Animal::create([
                'animal_id' => 'A' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => $animalNames[array_rand($animalNames)] . ' ' . $i,
                'tag_number' => 'T' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'breed' => $breeds[array_rand($breeds)],
                'gender' => 'female',
                'birth_date' => sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay),
                'weight' => rand(400, 800) + (rand(0, 99) / 100),
                'status' => 'active'
            ]);
        }

        // Create Employees
        $employees = [
            ['employee_id' => 'EMP001', 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@farm.com', 'position' => 'farm-manager', 'department' => 'administration', 'hire_date' => '2023-01-15', 'salary' => 5000],
            ['employee_id' => 'EMP002', 'first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2023-03-20', 'salary' => 3500],
            ['employee_id' => 'EMP003', 'first_name' => 'Mike', 'last_name' => 'Johnson', 'email' => 'mike@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2023-02-10', 'salary' => 4000],
            ['employee_id' => 'EMP004', 'first_name' => 'Sarah', 'last_name' => 'Williams', 'email' => 'sarah@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2023-04-05', 'salary' => 3500],
            ['employee_id' => 'EMP005', 'first_name' => 'David', 'last_name' => 'Brown', 'email' => 'david@farm.com', 'position' => 'maintenance', 'department' => 'maintenance', 'hire_date' => '2023-05-10', 'salary' => 3000],
            ['employee_id' => 'EMP006', 'first_name' => 'Emily', 'last_name' => 'Davis', 'email' => 'emily@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2023-06-15', 'salary' => 4000],
            ['employee_id' => 'EMP007', 'first_name' => 'Robert', 'last_name' => 'Wilson', 'email' => 'robert@farm.com', 'position' => 'admin', 'department' => 'administration', 'hire_date' => '2023-07-20', 'salary' => 3800],
            ['employee_id' => 'EMP008', 'first_name' => 'Lisa', 'last_name' => 'Martinez', 'email' => 'lisa@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2023-08-25', 'salary' => 3500],
            ['employee_id' => 'EMP009', 'first_name' => 'James', 'last_name' => 'Garcia', 'email' => 'james@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2023-09-01', 'salary' => 4000],
            ['employee_id' => 'EMP010', 'first_name' => 'Jennifer', 'last_name' => 'Anderson', 'email' => 'jennifer@farm.com', 'position' => 'maintenance', 'department' => 'maintenance', 'hire_date' => '2023-10-05', 'salary' => 3000],
            ['employee_id' => 'EMP011', 'first_name' => 'Michael', 'last_name' => 'Taylor', 'email' => 'michael@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2023-11-10', 'salary' => 3500],
            ['employee_id' => 'EMP012', 'first_name' => 'Linda', 'last_name' => 'Thomas', 'email' => 'linda@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2023-12-15', 'salary' => 4000],
            ['employee_id' => 'EMP013', 'first_name' => 'William', 'last_name' => 'Moore', 'email' => 'william@farm.com', 'position' => 'maintenance', 'department' => 'maintenance', 'hire_date' => '2024-01-20', 'salary' => 3000],
            ['employee_id' => 'EMP014', 'first_name' => 'Mary', 'last_name' => 'Jackson', 'email' => 'mary@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2024-02-25', 'salary' => 3500],
            ['employee_id' => 'EMP015', 'first_name' => 'Richard', 'last_name' => 'White', 'email' => 'richard@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2024-03-01', 'salary' => 4000],
            ['employee_id' => 'EMP016', 'first_name' => 'Patricia', 'last_name' => 'Harris', 'email' => 'patricia@farm.com', 'position' => 'admin', 'department' => 'administration', 'hire_date' => '2024-04-05', 'salary' => 3800],
            ['employee_id' => 'EMP017', 'first_name' => 'Thomas', 'last_name' => 'Martin', 'email' => 'thomas@farm.com', 'position' => 'milk-technician', 'department' => 'production', 'hire_date' => '2024-08-10', 'salary' => 3500],
            ['employee_id' => 'EMP018', 'first_name' => 'Nancy', 'last_name' => 'Thompson', 'email' => 'nancy@farm.com', 'position' => 'animal-care', 'department' => 'health', 'hire_date' => '2024-09-15', 'salary' => 4000],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        // Create Health Records for all animals
        $veterinarians = ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Davis'];
        $symptoms = [
            'None - routine checkup',
            'Slight decrease in appetite',
            'Mild lameness in left hind leg',
            'Reduced milk production',
            'Elevated temperature',
            'Respiratory issues',
            'Digestive problems',
            'Skin irritation'
        ];
        $treatments = [
            'None required',
            'Antibiotics prescribed - 5 day course',
            'Rest and monitoring recommended',
            'Anti-inflammatory medication',
            'Vitamin supplements',
            'Hoof trimming and care',
            'Dietary adjustment',
            'Topical ointment application'
        ];

        // Create 208 healthy records
        for ($i = 1; $i <= 208; $i++) {
            AnimalHealthRecord::create([
                'animal_id' => $i,
                'health_status' => 'healthy',
                'check_date' => now()->subDays(rand(1, 30)),
                'next_check_date' => now()->addDays(rand(20, 40)),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'temperature' => rand(1000, 1025) / 10, // 100.0 - 102.5
                'symptoms' => 'None - routine checkup',
                'treatment' => 'None required',
                'notes' => 'Animal in good health. Continue normal routine.'
            ]);
        }

        // Create 29 treatment records
        for ($i = 209; $i <= 237; $i++) {
            AnimalHealthRecord::create([
                'animal_id' => $i,
                'health_status' => 'treatment',
                'check_date' => now()->subDays(rand(1, 15)),
                'next_check_date' => now()->addDays(rand(5, 15)),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'temperature' => rand(1020, 1035) / 10, // 102.0 - 103.5
                'symptoms' => $symptoms[array_rand($symptoms)],
                'treatment' => $treatments[array_rand($treatments)],
                'notes' => 'Under treatment. Monitor closely and follow medication schedule.'
            ]);
        }

        // Create 8 critical records
        for ($i = 238; $i <= 245; $i++) {
            AnimalHealthRecord::create([
                'animal_id' => $i,
                'health_status' => 'critical',
                'check_date' => now()->subDays(rand(1, 7)),
                'next_check_date' => now()->addDays(rand(1, 5)),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'temperature' => rand(1035, 1055) / 10, // 103.5 - 105.5
                'symptoms' => 'Severe symptoms requiring immediate attention',
                'treatment' => 'Intensive care and close monitoring required',
                'notes' => 'CRITICAL - Requires immediate veterinary attention and 24/7 monitoring.'
            ]);
        }

        // Create Milk Production Records (Last 30 days for multiple animals)
        $qualityGrades = ['A', 'B', 'C'];
        for ($day = 30; $day >= 0; $day--) {
            $date = now()->subDays($day);
            // Create records for first 50 animals per day
            for ($animalId = 1; $animalId <= 50; $animalId++) {
                MilkProduction::create([
                    'animal_id' => $animalId,
                    'production_date' => $date,
                    'morning_quantity' => rand(10, 20) + (rand(0, 99) / 100),
                    'evening_quantity' => rand(8, 16) + (rand(0, 99) / 100),
                    'fat_content' => rand(30, 45) / 10, // 3.0 - 4.5%
                    'protein_content' => rand(28, 38) / 10, // 2.8 - 3.8%
                    'quality_grade' => $qualityGrades[array_rand($qualityGrades)],
                    'notes' => $day == 0 ? 'Today\'s production' : null
                ]);
            }
        }

        // Create Expense Records (Last 60 days)
        $expenseCategories = [
            'Feed' => ['Cattle feed purchase', 'Mineral supplements', 'Hay and silage'],
            'Medical' => ['Veterinary services', 'Vaccines', 'Medicines and treatments'],
            'Maintenance' => ['Equipment repair', 'Facility maintenance', 'Cleaning supplies'],
            'Utilities' => ['Electricity bill', 'Water bill', 'Gas/fuel'],
            'Labor' => ['Payroll expenses', 'Overtime payments', 'Staff benefits'],
            'Transportation' => ['Fuel costs', 'Vehicle maintenance', 'Delivery charges']
        ];

        $vendors = ['Feed Supply Co.', 'AgriVet Services', 'Farm Equipment Ltd.', 'Local Hardware', 'Utility Company'];
        $paymentMethods = ['cash', 'check', 'card', 'bank_transfer'];

        for ($day = 60; $day >= 0; $day--) {
            $numExpenses = rand(2, 5); // 2-5 expenses per day
            for ($i = 0; $i < $numExpenses; $i++) {
                $category = array_rand($expenseCategories);
                $descriptions = $expenseCategories[$category];

                Expense::create([
                    'category' => $category,
                    'description' => $descriptions[array_rand($descriptions)],
                    'amount' => rand(100, 2000) + (rand(0, 99) / 100),
                    'expense_date' => now()->subDays($day),
                    'vendor' => $vendors[array_rand($vendors)],
                    'receipt_number' => 'RCP-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'notes' => $day == 0 ? 'Recent expense' : null
                ]);
            }
        }

        // Create Income Records (Last 60 days)
        $customers = ['Local Dairy Co.', 'Regional Milk Distributor', 'Dairy Processing Plant', 'Direct Sales', 'Wholesale Buyer'];

        for ($day = 60; $day >= 0; $day--) {
            // Daily milk sales
            Income::create([
                'source' => 'milk_sales',
                'description' => 'Daily milk sales to distributor',
                'amount' => rand(800, 1500) + (rand(0, 99) / 100),
                'income_date' => now()->subDays($day),
                'customer' => $customers[array_rand($customers)],
                'quantity' => rand(200, 400),
                'unit' => 'liters',
                'notes' => $day == 0 ? 'Today\'s income' : null
            ]);

            // Occasional animal sales or other income
            if (rand(1, 10) > 7) { // 30% chance
                Income::create([
                    'source' => rand(0, 1) ? 'animal_sales' : 'other',
                    'description' => rand(0, 1) ? 'Sale of livestock' : 'Consulting services',
                    'amount' => rand(500, 3000) + (rand(0, 99) / 100),
                    'income_date' => now()->subDays($day),
                    'customer' => 'Various Buyers',
                    'quantity' => rand(1, 3),
                    'unit' => 'animals',
                    'notes' => null
                ]);
            }
        }

        // Create Attendance Records (Last 30 days for all employees)
        $statuses = ['present', 'present', 'present', 'present', 'late', 'absent', 'half_day']; // Weighted towards present

        for ($day = 30; $day >= 0; $day--) {
            $employees = Employee::all();
            foreach ($employees as $employee) {
                $status = $statuses[array_rand($statuses)];
                $checkIn = null;
                $checkOut = null;

                if ($status === 'present') {
                    $checkIn = sprintf('%02d:%02d', rand(7, 9), rand(0, 59));
                    $checkOut = sprintf('%02d:%02d', rand(16, 18), rand(0, 59));
                } elseif ($status === 'late') {
                    $checkIn = sprintf('%02d:%02d', rand(9, 10), rand(0, 59));
                    $checkOut = sprintf('%02d:%02d', rand(16, 18), rand(0, 59));
                } elseif ($status === 'half_day') {
                    $checkIn = sprintf('%02d:%02d', rand(7, 9), rand(0, 59));
                    $checkOut = sprintf('%02d:%02d', rand(11, 13), rand(0, 59));
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_date' => now()->subDays($day),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'notes' => $status === 'absent' ? 'Sick leave' : null
                ]);
            }
        }

        // Create Comprehensive Inventory Items
        $inventoryItems = [
            // Feed & Supplies
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Cattle Feed - Premium Mix', 'category' => 'cattle_feed', 'batch_number' => 'BATCH-FEED-001', 'quantity' => 1500, 'unit' => 'kg', 'unit_price' => 2.50, 'reorder_level' => 300, 'supplier' => 'Feed Supply Co.', 'manufacturer' => 'Premium Feed Manufacturers', 'last_restocked' => now()->subDays(5)],
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Hay Bales', 'category' => 'hay', 'batch_number' => 'BATCH-HAY-002', 'quantity' => 200, 'unit' => 'bales', 'unit_price' => 8.00, 'reorder_level' => 50, 'supplier' => 'Local Farm Supply', 'manufacturer' => 'Local Hay Producer', 'last_restocked' => now()->subDays(10)],
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Mineral Supplements', 'category' => 'supplements', 'batch_number' => 'BATCH-SUP-003', 'quantity' => 50, 'unit' => 'kg', 'unit_price' => 12.00, 'reorder_level' => 15, 'supplier' => 'AgriVet Services', 'manufacturer' => 'NutriSupply Co.', 'last_restocked' => now()->subDays(15)],
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Silage', 'category' => 'hay', 'batch_number' => 'BATCH-SIL-004', 'quantity' => 500, 'unit' => 'kg', 'unit_price' => 1.80, 'reorder_level' => 100, 'supplier' => 'Feed Supply Co.', 'manufacturer' => 'Silage Farms Ltd.', 'last_restocked' => now()->subDays(3)],
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Bedding Straw', 'category' => 'bedding', 'batch_number' => 'BATCH-BED-005', 'quantity' => 150, 'unit' => 'bales', 'unit_price' => 6.50, 'reorder_level' => 30, 'supplier' => 'Local Farm Supply', 'manufacturer' => 'Straw Suppliers Inc.', 'last_restocked' => now()->subDays(7)],

            // Medical Supplies
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Antibiotics - General', 'category' => 'antibiotic', 'batch_number' => 'BATCH-ABX-101', 'quantity' => 25, 'unit' => 'bottles', 'unit_price' => 45.00, 'reorder_level' => 5, 'supplier' => 'AgriVet Services', 'manufacturer' => 'PharmaVet Labs', 'last_restocked' => now()->subDays(20), 'expiry_date' => now()->addMonths(12)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Vaccination Doses', 'category' => 'vaccine', 'batch_number' => 'BATCH-VAC-102', 'quantity' => 100, 'unit' => 'vials', 'unit_price' => 8.50, 'reorder_level' => 20, 'supplier' => 'AgriVet Services', 'manufacturer' => 'Animal Health Vaccines Ltd.', 'last_restocked' => now()->subDays(25), 'expiry_date' => now()->addMonths(6)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'First Aid Supplies', 'category' => 'equipment', 'batch_number' => 'BATCH-AID-103', 'quantity' => 15, 'unit' => 'kits', 'unit_price' => 35.00, 'reorder_level' => 5, 'supplier' => 'Medical Supplies Inc.', 'manufacturer' => 'CareMed Equipment', 'last_restocked' => now()->subDays(30)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Disinfectants', 'category' => 'medicine', 'batch_number' => 'BATCH-DIS-104', 'quantity' => 40, 'unit' => 'liters', 'unit_price' => 15.00, 'reorder_level' => 10, 'supplier' => 'Cleaning Supply Co.', 'manufacturer' => 'CleanCare Solutions', 'last_restocked' => now()->subDays(12), 'expiry_date' => now()->addMonths(18)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Pain Reliever Tablets', 'category' => 'medicine', 'batch_number' => 'BATCH-PRN-105', 'quantity' => 500, 'unit' => 'tablets', 'unit_price' => 0.50, 'reorder_level' => 100, 'supplier' => 'AgriVet Services', 'manufacturer' => 'VetPharma Inc.', 'last_restocked' => now()->subDays(8), 'expiry_date' => now()->addMonths(24)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Vitamin B12 Injections', 'category' => 'supplement', 'batch_number' => 'BATCH-VIT-106', 'quantity' => 200, 'unit' => 'vials', 'unit_price' => 12.00, 'reorder_level' => 30, 'supplier' => 'AgriVet Services', 'manufacturer' => 'BioVet Nutrition', 'last_restocked' => now()->subDays(15), 'expiry_date' => now()->addMonths(9)],

            // Low Stock Items (for testing)
            ['inventory_type' => 'feed_supplies', 'item_name' => 'Emergency Feed Supplement', 'category' => 'supplements', 'batch_number' => 'BATCH-EMG-999', 'quantity' => 8, 'unit' => 'kg', 'unit_price' => 25.00, 'reorder_level' => 10, 'supplier' => 'Feed Supply Co.', 'manufacturer' => 'Emergency Feed Corp.', 'last_restocked' => now()->subDays(45)],
            ['inventory_type' => 'medical_supplies', 'item_name' => 'Hoof Care Kit', 'category' => 'other', 'batch_number' => 'BATCH-HOF-998', 'quantity' => 2, 'unit' => 'kits', 'unit_price' => 85.00, 'reorder_level' => 3, 'supplier' => 'AgriVet Services', 'manufacturer' => 'HoofCare Solutions', 'last_restocked' => now()->subDays(60), 'expiry_date' => now()->addYears(2)],
        ];

        foreach ($inventoryItems as $item) {
            Inventory::create($item);
        }

        // Create Vaccination Records
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

        // Vaccinate random animals from the last 3 months
        for ($i = 1; $i <= 60; $i++) {
            $daysAgo = rand(1, 90);
            $dateAdministered = now()->subDays($daysAgo);
            $nextDueDate = $dateAdministered->copy()->addMonths(rand(3, 12));

            Vaccination::create([
                'animal_id' => rand(1, 245),
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
                'animal_id' => rand(1, 245),
                'vaccine_name' => $vaccines[array_rand($vaccines)],
                'date_administered' => $dateAdministered,
                'next_due_date' => $nextDueDate,
                'batch_number' => 'BATCH-' . rand(1000, 9999),
                'veterinarian' => $veterinarians[array_rand($veterinarians)],
                'notes' => 'Follow-up vaccination required'
            ]);
        }

        // Create Milk Quality Tests
        echo "Creating milk quality tests...\n";
        $allAnimals = Animal::all();
        $testResults = ['Passed', 'Failed', 'Pending'];
        $qualityGrades = ['A', 'B', 'C', 'D'];
        $testers = ['Dr. Sarah Miller', 'Dr. John Davis', 'Lab Tech Maria Garcia', 'Lab Tech Tom Wilson'];

        // Create quality tests for the last 60 days
        for ($i = 60; $i >= 0; $i--) {
            $testDate = date('Y-m-d', strtotime("-$i days"));
            $numTests = rand(1, 3); // 1-3 tests per day

            for ($j = 0; $j < $numTests; $j++) {
                $fatContent = rand(30, 60) / 10; // 3.0 - 6.0%
                $proteinContent = rand(28, 45) / 10; // 2.8 - 4.5%
                $lactoseContent = rand(45, 52) / 10; // 4.5 - 5.2%
                $phLevel = rand(650, 680) / 100; // 6.50 - 6.80
                $temperature = rand(20, 60) / 10; // 2.0 - 6.0°C
                $somaticCellCount = rand(50000, 400000); // cells/mL

                // Determine quality grade based on parameters
                $qualityGrade = 'B';
                if ($fatContent >= 4.0 && $proteinContent >= 3.2 && $phLevel >= 6.6 && $phLevel <= 6.75 && $somaticCellCount <= 200000) {
                    $qualityGrade = 'A';
                } elseif ($fatContent < 3.5 || $proteinContent < 3.0 || $phLevel < 6.5 || $somaticCellCount > 300000) {
                    $qualityGrade = 'C';
                }

                // Determine test result
                $testResult = 'Passed';
                if ($qualityGrade === 'C' && rand(0, 1) === 1) {
                    $testResult = 'Failed';
                    $qualityGrade = 'D';
                } elseif (rand(0, 10) === 0) {
                    $testResult = 'Pending';
                }

                $batchNumber = 'BATCH-' . date('Ymd', strtotime($testDate)) . '-' . str_pad($j + 1, 3, '0', STR_PAD_LEFT);
                $randomAnimal = $allAnimals->random();

                MilkQualityTest::create([
                    'animal_id' => rand(0, 2) === 0 ? $randomAnimal->id : null, // 33% have specific animal
                    'test_date' => $testDate,
                    'batch_number' => $batchNumber,
                    'fat_content' => $fatContent,
                    'protein_content' => $proteinContent,
                    'lactose_content' => $lactoseContent,
                    'ph_level' => $phLevel,
                    'temperature' => $temperature,
                    'somatic_cell_count' => $somaticCellCount,
                    'quality_grade' => $qualityGrade,
                    'test_result' => $testResult,
                    'tested_by' => $testers[array_rand($testers)],
                    'notes' => $testResult === 'Failed' ? 'Sample failed quality standards' : ($testResult === 'Pending' ? 'Awaiting final analysis' : 'All parameters within acceptable range')
                ]);
            }
        }

        echo "Seeding completed successfully!\n";
    }
}
