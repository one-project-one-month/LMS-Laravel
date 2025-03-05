<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Exception;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {

        // INITIALIZE BASE QUERY
        $query = Instructor::query()->with('user:id,username,email,phone,address')->filter(request());



        $validSortColumns = ['id', 'email'];
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
            "message" => "instructors retrieved successfully.",
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

            $instructor = Instructor::find($id["id"]);
            if (!$instructor) {
                return response()->json([
                    "message" => "Instructor not found.",
                ], 404);
            }
            if ($instructor->user->is_available == false) {
                $instructor->user->update(["is_available" => true]);
                return response()->json([
                    "message" => "Instructor is  unsuspend successfully.",
                ]);
            } else {
                $instructor->user->update(["is_available" => false]);
                return response()->json([
                    "message" => "Instructor is  suspended successfully.",
                ]);
            }





        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }
}
