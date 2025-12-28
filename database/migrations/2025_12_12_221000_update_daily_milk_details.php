<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_milk_records', function (Blueprint $table) {
            $table->integer('sick_animals')->default(0)->after('total_buffaloes_milked');
            $table->integer('pregnant_animals')->default(0)->after('sick_animals');
            $table->integer('male_animals')->default(0)->after('pregnant_animals'); // For "kuch male h"
            $table->dropColumn('excluded_animals'); // Removing generic column
        });
    }

    public function down(): void
    {
        Schema::table('daily_milk_records', function (Blueprint $table) {
            $table->integer('excluded_animals')->default(0);
            $table->dropColumn(['sick_animals', 'pregnant_animals', 'male_animals']);
        });
    }
};
