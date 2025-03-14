<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Student;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        // $this->call(LessonSeeder::class);

        try {
            Admin::create([
                "user_id" => User::create([
                    "username" => "admin",
                    "email" => "admin@gmail.com",
                    "password" => Hash::make("admin1234"),
                    "role_id" => 3
                ])->id
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        Student::factory(5)->create();
        Category::factory(3)->create();
        Course::factory(5)->create();
        lesson::factory(5)->create();
    }
}
