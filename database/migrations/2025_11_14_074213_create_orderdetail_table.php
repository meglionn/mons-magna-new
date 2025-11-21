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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->id('OrderDetailID');
            $table->foreignId('OrderID')->nullable()->constrained('orders', 'OrderID');
            $table->foreignId('ProductID')->nullable()->constrained('products', 'ProductID');
            $table->integer('Jumlah');
            $table->decimal('HargaSatuan', 12, 2)->nullable();
            $table->decimal('Subtotal', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderdetails');
    }
};