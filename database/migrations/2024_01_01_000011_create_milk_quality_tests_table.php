<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milk_quality_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->nullable()->constrained('animals')->onDelete('set null');
            $table->date('test_date');
            $table->string('batch_number')->unique();
            $table->decimal('fat_content', 5, 2);
            $table->decimal('protein_content', 5, 2);
            $table->decimal('lactose_content', 5, 2)->nullable();
            $table->decimal('ph_level', 4, 2);
            $table->decimal('temperature', 5, 2);
            $table->integer('somatic_cell_count')->nullable();
            $table->enum('quality_grade', ['A', 'B', 'C', 'D'])->default('B');
            $table->enum('test_result', ['Passed', 'Failed', 'Pending'])->default('Pending');
            $table->string('tested_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('test_date');
            $table->index('quality_grade');
            $table->index('test_result');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milk_quality_tests');
    }
};

