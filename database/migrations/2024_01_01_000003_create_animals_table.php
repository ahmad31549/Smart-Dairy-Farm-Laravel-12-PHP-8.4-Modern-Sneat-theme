<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('animal_id')->unique();
            $table->string('name')->nullable();
            $table->string('tag_number')->unique();
            $table->enum('breed', ['holstein', 'jersey', 'guernsey', 'ayrshire']);
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->decimal('weight', 8, 2)->nullable();
            $table->enum('status', ['active', 'sold', 'deceased'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};