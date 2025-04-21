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
use Illuminate\Support\Facades\DB;
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
        $categories = [
            ['id' => 1, 'name' => 'Computer Science'],
            ['id' => 2, 'name' => 'C#'],
            ['id' => 3, 'name' => 'JavaScript'],
            ['id' => 4, 'name' => 'React'],
            ['id' => 5, 'name' => 'NextJS'],
            ['id' => 6, 'name' => 'PHP'],
            ['id' => 7, 'name' => 'Laravel'],
            ['id' => 8, 'name' => 'NestJs'],
            ['id' => 9, 'name' => 'Go'],
            ['id' => 10, 'name' => 'DevOps'],
        ];

     foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }

        Course::factory(4)->create();
        $this->call(LessonSeeder::class);
        
        Course::factory(5)->create();
    }
}
