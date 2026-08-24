<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movement_details', function (Blueprint $table) {
            $table->foreignId('item_variant_id')->nullable()->after('item_id')->constrained('item_variants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movement_details', function (Blueprint $table) {
            $table->dropForeign(['item_variant_id']);
            $table->dropColumn('item_variant_id');
        });
    }
};
