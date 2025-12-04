<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key
            $table->dropForeign(['CustomerID']);
            
            // Add new foreign key with cascade
            $table->foreign('CustomerID')
                  ->references('CustomerID')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['CustomerID']);
            
            $table->foreign('CustomerID')
                  ->references('CustomerID')
                  ->on('customers');
        });
    }
};