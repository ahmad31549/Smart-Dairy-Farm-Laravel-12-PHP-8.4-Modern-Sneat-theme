<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income', function (Blueprint $table) {
            $table->id();
            $table->enum('source', ['milk_sales', 'animal_sales', 'other']);
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('income_date');
            $table->string('customer')->nullable();
            $table->decimal('quantity', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income');
    }
};