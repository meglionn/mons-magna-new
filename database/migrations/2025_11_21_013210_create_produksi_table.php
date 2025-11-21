<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi', function (Blueprint $table) {
            $table->id('ProduksiID');
            $table->foreignId('OrderID')->nullable()->constrained('orders', 'OrderID');
            $table->date('TanggalMulai');
            $table->date('TanggalSelesai')->nullable();
            $table->string('StatusProduksi', 50)->default('Dalam Proses');
            $table->text('Keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi');
    }
};