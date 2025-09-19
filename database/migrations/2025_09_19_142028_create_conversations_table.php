<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();   // ad owner
            $table->foreignId('replier_id')->constrained('users')->cascadeOnDelete();  // person replying
            $table->enum('progress', ['0%', '25%', '50%', '75%', '100%'])->default('0%');
            $table->boolean('unlocked_photo')->default(false);
            $table->timestamps();

            $table->unique(['ad_id', 'author_id', 'replier_id']); // prevent duplicate conversations per ad
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
