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
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
        
            // either FK or raw email
            $table->uuid('user_id')->nullable(); // if linked to existing user
            $table->string('email')->index();    // fallback if no user yet
        
            $table->string('code');              // verification code
            $table->unsignedTinyInteger('attempts')->default(0); // retry count
            $table->timestamp('expires_at');
            $table->boolean('verified')->default(false);
        
            $table->timestamps();
        
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_verifications');
    }
};
