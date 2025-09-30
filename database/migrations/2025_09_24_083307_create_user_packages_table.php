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
        Schema::create('user_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('package_id');
            
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date');
            $table->enum('status', ['active', 'expired', 'pending'])->default('pending');
            
            $table->timestamps();
        
            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        
            $table->foreign('package_id')
                  ->references('id')->on('packages')
                  ->cascadeOnDelete();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_packages');
    }
};
