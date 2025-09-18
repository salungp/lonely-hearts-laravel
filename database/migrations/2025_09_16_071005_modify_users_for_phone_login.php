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
            // Make email nullable (if not already)
            $table->string('email')->nullable()->change();

            // Remove password column
            $table->dropColumn('password');

            // Add phone_number column if not exists
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->unique()->after('name');
            }

            // Add phone_verified_at column
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->after('email');
            $table->dropColumn('phone_number');
            $table->dropColumn('phone_verified_at');
        });
    }
};
