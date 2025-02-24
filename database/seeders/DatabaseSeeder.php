<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Instructor;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory(5)->has(Instructor::factory())->create();
        Category::factory(3)->create();
        Course::factory(4)->create();
        // Instructor::factory(4)->create();
    }
}
