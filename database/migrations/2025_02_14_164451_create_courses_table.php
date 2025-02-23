<?php

use App\Models\Category;
use App\Models\Instructor;
use App\Models\User;
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
            $table->string('course_name');
            $table->string('thumbnail')->nullable();
            $table->boolean('is_available')->default(false);
            $table->enum('type', ["free", "paid"])->default("free");
            $table->enum('level', ["beginner", "intermediate", "advance"])->default("beginner");
            $table->longText('description')->nullable();
            $table->string('duration');
            $table->string('original_price'); // calculation work at frontend
            $table->string('current_price');
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnUpdate();
            $table->foreignIdFor(Instructor::class)->constrained()->cascadeOnDelete();
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
