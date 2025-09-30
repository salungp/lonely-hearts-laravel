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
        Schema::create('packages', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID PK
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // price with 2 decimals
            $table->unsignedInteger('duration_days'); // enforce unsigned (no negative)
            $table->json('benefits')->nullable(); // flexible benefits (array/object)
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
