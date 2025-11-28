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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'EmailVerifiedAt')) {
                $table->timestamp('EmailVerifiedAt')->nullable()->after('Email');
            }
            if (!Schema::hasColumn('users', 'EmailVerificationToken')) {
                $table->string('EmailVerificationToken', 64)->nullable()->after('EmailVerifiedAt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'EmailVerificationToken')) {
                $table->dropColumn('EmailVerificationToken');
            }
            if (Schema::hasColumn('users', 'EmailVerifiedAt')) {
                $table->dropColumn('EmailVerifiedAt');
            }
        });
    }
};
