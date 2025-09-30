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
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
        
            $table->uuid('user_id')->nullable();  // optional link to users.id
            $table->string('phone')->index();     // not unique, so multiple attempts allowed
            $table->string('otp');                // hashed OTP
            $table->unsignedTinyInteger('attempts')->default(0);
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
        Schema::dropIfExists('phone_verifications');
    }
};
