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
        Schema::create('photos', function (Blueprint $table) {
            $table->uuid('id')->primary();   // UUID PK
            $table->uuid('ad_id');           // FK to ads.id
        
            $table->string('file_path');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
        
            $table->timestamps();
        
            // foreign key constraint
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
        Schema::dropIfExists('table_photos');
    }
};
