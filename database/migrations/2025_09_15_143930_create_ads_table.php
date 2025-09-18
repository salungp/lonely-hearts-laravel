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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('location');
            $table->string('box_number', 6)->unique();
            $table->unsignedBigInteger('views')->default(0);

            // snapshot fields
            $table->unsignedTinyInteger('snapshot_age');
            $table->string('snapshot_name');
            $table->string('snapshot_occupation');
            $table->string('snapshot_status');
            $table->enum('snapshot_gender', ['male', 'female', 'other']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
