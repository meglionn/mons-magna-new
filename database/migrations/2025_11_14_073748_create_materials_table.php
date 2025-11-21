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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('MaterialID');
            $table->string('NamaBahan', 100);
            $table->string('Kategori', 50)->nullable();
            $table->integer('StokBahan');
            $table->integer('MinimumStok')->default(10);
            $table->decimal('HargaSatuan', 12, 2)->nullable();
            $table->string('JenisBahan', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};