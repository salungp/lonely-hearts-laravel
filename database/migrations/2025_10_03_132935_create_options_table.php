<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category', 50)->index();   // e.g. profile, reply, ads
            $table->string('title', 100);              // e.g. gender, status
            $table->string('text')->nullable();        // e.g. "Looking to meet a"
            $table->enum('input_type', ['dropdown','text','textarea','multi'])
                  ->default('dropdown');
            $table->json('value')->nullable();         // store array/object or null for text
            $table->integer('sort_order')->default(0); // order in UI
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};

