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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('name');
            $table->string('thumbnail')->nullable();
            $table->longText('description')->nullable();
            $table->integer('is_available')->default(1);
            $table->string('type');
            $table->string('level');
            $table->integer('duration');
            $table->integer('original_price');
            $table->integer('current_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
