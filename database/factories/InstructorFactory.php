<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instructor>
 */
class InstructorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory(["role_id" => 2]),
            'nrc' => $this->faker->randomNumber(6, true),
            'edu_background' => $this->faker->randomElement(['Bachelor\'s Degree', 'Master\'s Degree', 'PhD'])
        ];
    }
}
