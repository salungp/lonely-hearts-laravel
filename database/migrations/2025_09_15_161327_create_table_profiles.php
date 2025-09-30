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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID PK
            $table->uuid('user_id');       // FK to users.id
        
            $table->string('display_name');
            $table->string('location');
            $table->unsignedTinyInteger('age');
            $table->string('occupation')->nullable();
            $table->string('status')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('bio')->nullable();
        
            $table->timestamps();
        
            // Foreign key constraint
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
        Schema::dropIfExists('table_profiles');
    }
};
