<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "course_name" => $this->faker->jobTitle(),
            "thumbnail" => $this->faker->imageUrl(),
            "duration" => $this->faker->randomElement(["7hours", "5hours"]),
            "original_price" => $this->faker->randomElement(["$200", "$300", "$600"]),
            "current_price" => $this->faker->randomElement(["$200", "$300", "$600"]),
            "category_id" => Category::factory(),
            "instructor_id" => Instructor::factory()
        ];
    }
}
