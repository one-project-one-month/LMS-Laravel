<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class StudentController extends Controller
{
    public function test()
    {
        $students = Student::with('user:username,email')
            ->orderBy('id', 'desc')
            ->paginate(10);



        return response()->json([
            "message" => "Students retrieved successfully.",
            "data" => [
                "students" => $students
            ]
        ]);
    }
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
        $query = User::query()->where("role_id", $student_role_id);

        // SEARCH FUNCTIONALITY

        $searchTerm = $request->input('q');
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('username', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                    ->orWhere('dob', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%');
            });
        }


        // FILTER FUNCTIONALITY
        // is_active = true or false
        $filterBy = $request->input('filter_by');
        $filterVal = $request->input('filter_val');

        if ($filterBy && $filterVal) {
            $query->where($filterBy, $filterVal);
        }
        // SORT FUNCTIONALITY

        $validSortColumns = ['id', 'email', 'dob'];
        $sortBy = in_array($request->input('sort_by'), $validSortColumns, true) ? $request->input('sort_by') : 'id';
        $sortDirection = in_array($request->input('sort_direction'), ['asc', 'desc'], true) ? $request->input('sort_direction') : 'desc';
        $query->orderBy($sortBy, $sortDirection);






        // LOAD RELATIONSHIPS

        // $query->with('user.addresses')->whereHas('user', function ($query) {
        //     $query->where('role', 'customer'); // Filtering by a specific column in the 'users' table
        // });


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
}
