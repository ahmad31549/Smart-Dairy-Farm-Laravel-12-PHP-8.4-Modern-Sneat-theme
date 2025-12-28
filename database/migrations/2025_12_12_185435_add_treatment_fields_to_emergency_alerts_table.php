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
        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->text('treatment_notes')->nullable()->after('doctor_advice');
        });
        
        DB::statement("ALTER TABLE emergency_alerts MODIFY COLUMN status ENUM('pending', 'forwarded_to_doctor', 'advised', 'resolved', 'visit_confirmed', 'on_site', 'under_treatment') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_alerts', function (Blueprint $table) {
            $table->dropColumn('treatment_notes');
        });

        DB::statement("ALTER TABLE emergency_alerts MODIFY COLUMN status ENUM('pending', 'forwarded_to_doctor', 'advised', 'resolved', 'visit_confirmed', 'on_site') NOT NULL DEFAULT 'pending'");
    }
};
