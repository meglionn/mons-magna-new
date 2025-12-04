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
        Schema::table('orderdetails', function (Blueprint $table) {
            $table->string('Ukuran')->nullable()->after('Subtotal');
            $table->string('Warna')->nullable()->after('Ukuran');
            $table->string('JenisBahan')->nullable()->after('Warna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderdetails', function (Blueprint $table) {
            $table->dropColumn(['Ukuran', 'Warna', 'JenisBahan']);
        });
    }
};
