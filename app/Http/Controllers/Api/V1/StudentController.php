<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class StudentController extends Controller
{
    public function show($id)
    {
        $student = Student::with('user:username,email')->find($id);
        return response()->json([
            "message" => "Student retrieved successfully.",
            "data" => [
                "student" => $student
            ]
        ]);
    }

    public function index(Request $request)
    {

        // INITIALIZE BASE QUERY
        $student_role_id = Role::where("role", "student")->first()->id;
        $query = Student::query()->with("user", function ($query) use ($student_role_id) {
            $query->select(["id", "username", "email", "dob", "phone", "address", "role_id"])->where("role_id", $student_role_id);
        })->filter(request());



        $validSortColumns = ['id', 'email', 'dob'];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $query->orderBy($sortBy, $sortDirection);









        // PAGINATION

        $limit = $request->input('limit', 10);
        $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? (int) $limit : 10;

        $students = $query->paginate($limit);


        // APPEND QUERY PARAMETERS TO PAGINATION LINKS

        // $students->appends([
        //     'q' => $searchTerm,
        //     'sort_by' => $sortBy,
        //     'sort_direction' => $sortDirection,
        //     'limit' => $limit,
        //     'filter_by' => $filterBy,
        //     'filter_val' => $filterVal,
        // ]);


        // RETURN RESULTS

        return response()->json([
            "message" => "Students retrieved successfully.",
            "data" => [
                "students" => $students
            ]
        ]);
    }
    public function suspend(Request $request)
    {

        try {
            $id = $request->validate([
                "id" => "required|exists:students,id"
            ]);

            $student = Student::find($id["id"]);
            if (!$student) {
                return response()->json([
                    "message" => "Student not found.",
                ], 404);
            }
            if ($student->user->is_available == false) {
                $student->user->update(["is_available" => true]);
                return response()->json([
                    "message" => "Student   is  unsuspend successfully.",
                ]);
            } else {
                $student->user->update(["is_available" => false]);
                return response()->json([
                    "message" => "Student   is  suspended successfully.",
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }
}
