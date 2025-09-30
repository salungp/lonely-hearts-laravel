<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ad_id');
            $table->uuid('author_id');
            $table->uuid('replier_id');
            $table->enum('progress', ['0%', '25%', '50%', '75%', '100%'])->default('0%');
            $table->boolean('unlocked_photo')->default(false);
            $table->timestamps();
        
            $table->unique(['ad_id', 'author_id', 'replier_id']);
        
            $table->foreign('ad_id')->references('id')->on('ads')->cascadeOnDelete();
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('replier_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
