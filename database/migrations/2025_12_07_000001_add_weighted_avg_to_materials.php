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
        // Add columns to track weighted average price calculation
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('TotalNilaiInventori', 14, 2)->default(0)->after('HargaSatuan');
            // TotalNilaiInventori = StokBahan * HargaSatuan
            // When user adds stock: 
            // new_total_value = (old_stock * old_price) + (new_stock * new_price)
            // new_avg_price = new_total_value / new_total_stock
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('TotalNilaiInventori');
        });
    }
};
