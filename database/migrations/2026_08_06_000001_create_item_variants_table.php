<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};
