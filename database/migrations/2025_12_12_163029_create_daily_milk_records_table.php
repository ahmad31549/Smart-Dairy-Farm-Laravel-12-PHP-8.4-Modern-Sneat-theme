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
        Schema::create('daily_milk_records', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('total_milk_quantity', 8, 2); // Liters
            $table->integer('total_buffaloes_milked');
            $table->integer('total_herd_size'); // Reserved count (e.g. 208)
            $table->integer('excluded_animals')->default(0); // Pregnant/Dry animals
            $table->foreignId('recorded_by')->constrained('users'); // Farm Worker
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_milk_records');
    }
};
