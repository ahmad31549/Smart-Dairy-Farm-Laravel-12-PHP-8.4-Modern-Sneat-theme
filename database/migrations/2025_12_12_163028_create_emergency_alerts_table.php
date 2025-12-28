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
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The Farm Worker
            $table->foreignId('animal_id')->nullable()->constrained()->onDelete('set null'); // Optional related animal
            $table->text('message'); // Description of the situation
            $table->text('doctor_advice')->nullable(); // Response from vet
            $table->enum('status', ['pending', 'forwarded_to_doctor', 'advised', 'resolved'])->default('pending');
            $table->boolean('is_forwarded')->default(false); // Admin forwarded to Vet
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
    }
};
