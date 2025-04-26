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
            "thumbnail" => $this->faker->randomElement([
                "https://images.pexels.com/photos/256401/pexels-photo-256401.jpeg", // Student writing in notebook
                "https://images.pexels.com/photos/1181671/pexels-photo-1181671.jpeg", // Books on a wooden table
                "https://images.pexels.com/photos/4145190/pexels-photo-4145190.jpeg", // Classroom with students
                "https://images.pexels.com/photos/5212327/pexels-photo-5212327.jpeg", // Teacher explaining on whiteboard
                "https://images.pexels.com/photos/4145191/pexels-photo-4145191.jpeg", // Students raising hands
                "https://images.pexels.com/photos/5212331/pexels-photo-5212331.jpeg", // Group study session
                "https://images.pexels.com/photos/3184328/pexels-photo-3184328.jpeg", // Online learning setup
                "https://images.pexels.com/photos/4145193/pexels-photo-4145193.jpeg", // Student reading a book
                "https://images.pexels.com/photos/5212332/pexels-photo-5212332.jpeg", // Graduation ceremony
                "https://images.pexels.com/photos/4145194/pexels-photo-4145194.jpeg", // Teacher with students
            ]),
            "duration" => $this->faker->randomElement(["7hours", "5hours"]),
            "original_price" => $this->faker->randomElement(["200", "300", "600"]),
            "current_price" => $this->faker->randomElement(["200", "300", "600"]),
            "category_id" => Category::factory(),
            "instructor_id" => Instructor::factory(),
            "is_available" => true
        ];
    }
}
