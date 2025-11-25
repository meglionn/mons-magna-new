<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('TransaksiID');
            $table->string('OrderID')->nullable();
            $table->enum('JenisTransaksi', ['Pemasukan', 'Pengeluaran']);
            $table->string('Kategori')->nullable();
            $table->decimal('Jumlah', 12, 2);
            $table->date('Tanggal');
            $table->string('MetodePembayaran')->nullable();
            $table->string('Status')->default('Completed');
            $table->text('Keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};