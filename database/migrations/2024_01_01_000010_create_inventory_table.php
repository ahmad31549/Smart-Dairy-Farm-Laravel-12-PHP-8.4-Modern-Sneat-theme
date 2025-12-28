<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('category');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('reorder_level', 10, 2);
            $table->string('supplier')->nullable();
            $table->date('last_restocked')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};