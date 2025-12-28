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
        Schema::table('inventory', function (Blueprint $table) {
            $table->enum('inventory_type', ['feed_supplies', 'medical_supplies'])->default('feed_supplies')->after('category');
            $table->string('batch_number')->nullable()->after('inventory_type');
            $table->string('manufacturer')->nullable()->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn(['inventory_type', 'batch_number', 'manufacturer']);
        });
    }
};
