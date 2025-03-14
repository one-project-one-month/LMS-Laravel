<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AdminDashboardInterface;


class AdminDashboardReposity implements AdminDashboardInterface
{
    public function getAllAdmins(Request $request)
    {
        $limit = $this->getLimit($request);
        $admins = User::admins()->latest()->paginate($limit);

        return $admins;
    }

    public function getAllStudents(Request $request)
    {
        $limit = $this->getLimit($request);
        $students = User::students()->latest()->paginate($limit);

        return $students;
    }

    public function getAllInstructors(Request $request)
    {
        $limit = $this->getLimit($request);
        $instructors = Instructor::latest()->with('user')->paginate($limit);

        return $instructors;
    }

    public function getCourses(Request $request)
    {
        $limit = $this->getLimit($request);
        $query = Course::latest();

        if (is_('instructor')) {
            $instructorId = Auth::user()->instructor->id;
            $query->where('instructor_id', $instructorId);
        }

        $courses = $query->paginate($limit);

        return $courses;
    }

    public function getStudentsFromCourse(int $id, Request $request)
    {
        $course = Course::findOrFail($id);
        $limit = $this->getLimit($request);

        $is_completed = $request->has('is_completed') ? filter_var($request->is_completed, FILTER_VALIDATE_BOOLEAN) : null;

        $query = $course->students()->with('user');

        if ($is_completed !== null) {
            $query->wherePivot('is_completed', $is_completed);
        }

        $students = $query->paginate($limit);

        return $students;
    }

    private function getLimit(Request $request)
    {
        return ($request->has('limit') && is_numeric($request->limit) <= 100) ? (int) $request->limit : 20;
    }
}
