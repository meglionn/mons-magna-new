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
        Schema::create('customdetails', function (Blueprint $table) {
            $table->id('CustomID');
            $table->foreignId('OrderID')->constrained('orders', 'OrderID');
            $table->string('JenisBahan', 100)->nullable();
            $table->string('Warna', 50)->nullable();
            $table->integer('Ukuran')->nullable();
            $table->string('Model', 100)->nullable();
            $table->text('CatatanTambahan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customdetails');
    }
};