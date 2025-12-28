<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('milk_production', function (Blueprint $table) {
            $table->id();
            $table->foreignId('animal_id')->constrained()->onDelete('cascade');
            $table->date('production_date');
            $table->decimal('morning_quantity', 8, 2)->default(0);
            $table->decimal('evening_quantity', 8, 2)->default(0);
            $table->decimal('total_quantity', 8, 2)->storedAs('morning_quantity + evening_quantity');
            $table->decimal('fat_content', 5, 2)->nullable();
            $table->decimal('protein_content', 5, 2)->nullable();
            $table->enum('quality_grade', ['A', 'B', 'C'])->default('A');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milk_production');
    }
};