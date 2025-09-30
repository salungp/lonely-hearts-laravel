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
        Schema::create('likes', function (Blueprint $table) {
            $table->uuid('id')->primary(); // optional, can also skip if you want composite PK
            $table->uuid('user_id');
            $table->uuid('ad_id');
            $table->timestamps();
        
            // prevent duplicate likes
            $table->unique(['user_id', 'ad_id']);
        
            // foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        
            $table->foreign('ad_id')
                  ->references('id')->on('ads')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('like');
    }
};
