<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfNotExists('animals', 'status');
        $this->addIndexIfNotExists('animals', 'breed');
        $this->addIndexIfNotExists('animals', 'animal_id');

        $this->addIndexIfNotExists('animal_health_records', 'health_status');
        $this->addIndexIfNotExists('animal_health_records', 'check_date');
        $this->addIndexIfNotExists('animal_health_records', 'animal_id');

        $this->addIndexIfNotExists('vaccinations', 'status');
        $this->addIndexIfNotExists('vaccinations', 'date_administered');
        $this->addIndexIfNotExists('vaccinations', 'animal_id');

        $this->addIndexIfNotExists('expenses', 'expense_date');
        $this->addIndexIfNotExists('expenses', 'category');

        $this->addIndexIfNotExists('income', 'income_date');
        $this->addIndexIfNotExists('income', 'source');

        $this->addIndexIfNotExists('attendance', 'attendance_date');
        $this->addIndexIfNotExists('attendance', 'status');
        $this->addIndexIfNotExists('attendance', 'employee_id');

        // Note: Milk Quality Tests already has indexes in its create migration or via previous runs.
        // We skip it to avoid duplicates, but we can check if needed.
        $this->addIndexIfNotExists('milk_quality_tests', 'test_date');
        $this->addIndexIfNotExists('milk_quality_tests', 'test_result');
        $this->addIndexIfNotExists('milk_quality_tests', 'quality_grade');

        $this->addIndexIfNotExists('daily_milk_records', 'date');
        $this->addIndexIfNotExists('daily_milk_records', 'recorded_by');
        
        $this->addIndexIfNotExists('emergency_alerts', 'status');
        $this->addIndexIfNotExists('emergency_alerts', 'created_at');
        $this->addIndexIfNotExists('emergency_alerts', 'user_id');
        $this->addIndexIfNotExists('emergency_alerts', 'animal_id');
    }

    protected function addIndexIfNotExists($table, $column)
    {
        if (Schema::hasTable($table)) {
            $indexName = $table . '_' . $column . '_index';
            
            // Check if index exists by name
            $hasIndex = collect(Schema::getIndexes($table))->contains(function ($index) use ($indexName) {
                return $index['name'] === $indexName;
            });

            if (!$hasIndex) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->index($column);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can safely try dropping, if it fails it's fine in down
        $tables = [
            'animals' => ['status', 'breed', 'animal_id'],
            'animal_health_records' => ['health_status', 'check_date', 'animal_id'],
            'vaccinations' => ['status', 'date_administered', 'animal_id'],
            'expenses' => ['expense_date', 'category'],
            'income' => ['income_date', 'source'],
            'attendance' => ['attendance_date', 'status', 'employee_id'],
            'daily_milk_records' => ['date', 'recorder_id'],
            'emergency_alerts' => ['status', 'created_at', 'user_id', 'animal_id'],
            'milk_quality_tests' => ['test_date', 'test_result', 'quality_grade']
        ];

        foreach ($tables as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($columns) {
                    foreach ($columns as $column) {
                        try {
                            $table->dropIndex([$column]);
                        } catch (\Exception $e) {}
                    }
                });
            }
        }
    }
};
