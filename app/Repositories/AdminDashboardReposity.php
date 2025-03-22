<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminDashboardReposity
{
    public function getAllAdmins($limit)
    {
        $admins = User::admins()->latest()->paginate($limit);
        return $admins;
    }

    public function getAllStudents($limit)
    {
        $students = User::students()->latest()->paginate($limit);

        return $students;
    }

    public function getAllInstructors($limit)
    {
        $instructors = Instructor::latest()->with('user')->paginate($limit);

        return $instructors;
    }

    public function getCourses($limit)
    {
        $query = Course::latest();

        if (is_('instructor')) {
            $instructorId = Auth::user()->instructor->id;
            $query->where('instructor_id', $instructorId);
        }

        $courses = $query->paginate($limit);

        return $courses;
    }

    public function getStudentsFromCourse(int $id, $limit , $isComplete)
    {
        $course = Course::findOrFail($id);
        $query = $course->students()->with('user');

        if ($isComplete !== null) {
            $query->wherePivot('is_completed', $isComplete);
        }

        $students = $query->paginate($limit);

        return $students;
    }

}
