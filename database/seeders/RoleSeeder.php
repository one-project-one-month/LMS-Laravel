<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Mockery\Generator\StringManipulation\Pass\Pass;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["student", "instructor", "admin"];

        try {
            foreach ($roles as $role) {
                Role::create([
                    'role' => $role
                ]);
            };
        } catch (\Exception $e) {
           
        }
    }
}
