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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');            // who paid
            $table->uuid('user_package_id');    // which subscription/package this payment is for
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending'); // e.g. pending, completed, failed
            $table->string('method')->nullable();         // e.g. stripe, paypal, bank
            $table->string('transaction_ref')->nullable(); // gateway transaction ID
            $table->timestamps();
        
            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        
            $table->foreign('user_package_id')
                  ->references('id')->on('user_packages')
                  ->cascadeOnDelete();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
