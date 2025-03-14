<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface AdminDashboardInterface
{
    public function getAllAdmins(Request $request);

    public function getAllStudents(Request $request);

    public function getAllInstructors(Request $request);

    public function getCourses(Request $request);

    public function getStudentsFromCourse(int $id,Request $request);

}
