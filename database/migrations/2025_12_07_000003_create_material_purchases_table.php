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
        Schema::create('material_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('MaterialID');
            $table->integer('Jumlah');
            $table->decimal('HargaSatuan', 14, 2);
            $table->decimal('Total', 16, 2);
            $table->string('Supplier')->nullable();
            $table->timestamp('Tanggal')->useCurrent();
            $table->unsignedBigInteger('CreatedBy')->nullable();
            $table->text('Catatan')->nullable();

            $table->foreign('MaterialID')->references('MaterialID')->on('materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_purchases');
    }
};
