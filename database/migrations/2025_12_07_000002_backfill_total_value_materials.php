<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill TotalNilaiInventori for existing records
        DB::statement('UPDATE materials SET TotalNilaiInventori = COALESCE(StokBahan,0) * COALESCE(HargaSatuan,0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally reset to 0
        DB::statement('UPDATE materials SET TotalNilaiInventori = 0');
    }
};
