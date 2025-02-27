<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Can;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    /**
     *  Get all courses
     *  get - /api/courses
     */
    public function index(Request $request)
    {

        try {
            $query = Course::query();
            $searchParam = $request->input('q');
            if ($searchParam) {
                $query->where(function ($q) use ($searchParam) {
                    $q->where('course_name', "like", "%$searchParam%")
                        ->orWhere("description", "like", "%$searchParam%");
                });
            };

        $courses = Course::latest()->filter(request(['search','type','level','category','instructor']))->get();



            $filterBy = $request->input("filter_by");
            $filterValue = $request->input("filter_value");
            if ($filterBy && $filterValue) {
                $query->where($filterBy, $filterValue);
            }

            $validSortColumns = ['id', 'price', 'created_at'];
            $sortBy = in_array($request->input("sort_by"), $validSortColumns, true) ? $request->input("sort_by") : "id";
            $sortDirection = in_array($request->input("sort_direction"), $validSortColumns, true) ? $request->input("sort_direction") : "desc";
            $query->orderBy($sortBy, $sortDirection);

            $limit = $request->input("limit", 10);
            $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? $limit : 10;
            $courses = $query->paginate($limit);
            return response()->json([
                "message" => "Courses retrieved successfully.",
                "data" => [
                    "courses" => $courses
                ],

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed to load courses",
                "error" => $e->getMessage(),
            ], 400);
        }
    }

    /**
     *  store courese
     *  post - /api/courses
     *  @param - instructor_id, course_name, thumbnail, type, level, description, duration, original_price, current_price, category_id
     */
    public function store(CourseRequest $courseRequest)
    {
        $data = Arr::except($courseRequest->validated(), ["thumbnail"]);
        $image = Arr::only($courseRequest->validated(), ['thumbnail']);
        $user = JWTAuth::parseToken()->authenticate();
        $id = $user->instructor->id;

        // Get the uploaded file from the 'thumbnail' key
        $file = $image['thumbnail'];
        $path = $file->storeAs('thumbnails', time() . "$" . $user->id  .  Str::snake($data["course_name"])  . "." . $file->getClientOriginalExtension(), 'public');
        // $imageUrl = asset('storage/' . $path);
        $imageUrl = url(Storage::url($path));
        // $data["thumbnail"] = $imageUrl;

        $course = Course::create(array_merge($data, ["thumbnail" => $imageUrl], ["instructor_id" => $id]));

        return response()->json([
            "message" => "Course created successfully.",
            "data" => [
                "course" => $course
            ],

        ], 201);
    }

    /**
     *  store courese
     *  put - /api/courses/:id
     *  @param id
     *  @param request
     */
    public function update(CourseRequest $courseRequest, Course $course)
    {
        $attributes = $courseRequest->validated();


        if (!$course) {
            return response()->json([
                'message' => "Course not found.",
                "status" => 404
            ], 404);
        }

        $course->update($attributes);

        return response()->json([
            "message" => "Course updated successfully.",
            "data" => [
                "courses" => $course
            ],
            "status" => 200
        ], 200);
    }

    /**
     *  delete course
     *  delete - /api/courses/:id
     * @param id
     * @param request
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => "Course not found.",
                "status" => 404
            ], 404);
        }
        $course->delete();

        return response()->json([
            "message" => "delete successfully",
            "status" => 200
        ], 200);
    }
    public function show(Course $course)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $isEnrolled = false;
        return  Gate::allows("course_details", $course);

        if (is_("student") or Gate::allows("course_details", $course)) {
            $student =  $user->student;
            $courseLists = $student->courses;
            foreach ($courseLists as $list) {
                if ($list->id  === $course->id) {
                    $isEnrolled = true;
                    break;
                }
            }
            if ($isEnrolled or Gate::allows("course_details", $course)) {
                $result = array_merge($course->toArray(), ["lessons" => $course->lessons->toArray()]);
                return response()->json([
                    "message" => "success",
                    "data" => [
                        "course" => $result
                    ]
                ]);
            }
        } else {
            $result = Course::with(['lessons' => function ($query) {
                $query->select("title", "course_id")->where("is_available", true);
            }])->find($course->id);


            return response()->json([
                "message" => "success",
                "data" => [
                    "course" => $result
                ]
            ]);
        }
    }
}
