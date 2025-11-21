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
            $table->foreignId('OrderID')->nullable()->constrained('orders', 'OrderID');
            $table->enum('JenisTransaksi', ['Pemasukan', 'Pengeluaran']);
            $table->decimal('Jumlah', 12, 2);
            $table->date('Tanggal');
            $table->text('Keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};