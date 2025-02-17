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
            // $table->foreignIdFor(Instructor::class)->constrained()->cascadeOnDelete();
            // $table->foreignIdFor(Instructor::class);
            $table->string('course_name');
            $table->string('thumbnail');
            $table->boolean('is_available')->default(false);
            $table->enum('type', ["free", "paid"]);
            $table->enum('level', ["beginner", "intermediate", "advance"]);
            $table->longText('description')->nullable();
            $table->string('duration');
            $table->string('original_price'); // calculation work at frontend
            $table->string('current_price');
            $table->foreignIdFor(Category::class);
            // $table->foreignIdFor(Category::class)->constrained()->cascadeOnUpdate();
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
