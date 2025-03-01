<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
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
}
