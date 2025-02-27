<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "course_id" => 1,
            "title" => $this->faker->jobTitle(),
            "video_url" => $this->faker->url(),
            "lesson_detail" => $this->faker->text(),
            "is_available" => $this->faker->boolean(),
        ];
    }
}
