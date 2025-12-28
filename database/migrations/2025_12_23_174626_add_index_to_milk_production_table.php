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
        Schema::table('milk_production', function (Blueprint $table) {
            $table->index('production_date');
            $table->index('animal_id');
            $table->index('quality_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milk_production', function (Blueprint $table) {
            $table->dropIndex(['production_date']);
            $table->dropIndex(['animal_id']);
            $table->dropIndex(['quality_grade']);
        });
    }
};
