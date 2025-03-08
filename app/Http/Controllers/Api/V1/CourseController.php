<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Mail\CourseCreated;
use App\Models\Course;
use App\Models\Instructor;
use Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Can;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    //set student for complement 
    //* all done
    public function complete(Request $request, Course $course)
    {



        try {
            $attributes = $request->validate([
                "user_id" => "required|exists:students,id",
            ]);
            if (is_enrolled($attributes["user_id"], $course->id)) {
                if (Gate::allows("completeCourse", $course)) {
                    DB::table('enrollments')->where("user_id", $attributes["user_id"])->where("course_id", $course->id)->update(["is_completed" => true]);
                    return response()->json([
                        "message" =>  "success 🎉",
                    ]);
                }
            } else {
                return response()->json([
                    "message" => "Student is not enrolled the course!"
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }


    /**
     *  Get all courses
     *  get - /api/courses
     */
    //* done 
    public function index(Request $request)
    {
        try {
            $query = Course::query()->filter(request())->with(["instructorUser" => function ($query) {
                $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
            }, "category:id,name"]);
            $validSortColumns = ['id', 'current_price', 'created_at'];
            $sortBy = in_array($request->input("sort_by"), $validSortColumns, true) ? $request->input("sort_by") : "id";
            $sortDirection =   $request->input("sort_direction") ?? "desc";
            $query->orderBy($sortBy, $sortDirection);

            $limit = $request->input("limit", 10);
            $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? $limit : 10;
            $courses = $query->paginate($limit);


            return  new CourseCollection($courses);
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

    //* doing
    public function store(CourseRequest $courseRequest)
    {
    return "store";
        // try {
        //     $data = Arr::except($courseRequest->validated(), ["thumbnail"]);
        //     $image = Arr::only($courseRequest->validated(), ['thumbnail']);
        //     $user = JWTAuth::parseToken()->authenticate();
        //     $id = $user->instructor->id;

        //     // Get the uploaded file from the 'thumbnail' key
        //     $file = $image['thumbnail'];
        //     $path = $file->storeAs('thumbnails', time() . "$" . $user->id  .  Str::snake($data["course_name"])  . "." . $file->getClientOriginalExtension(), 'public');
        //     // $imageUrl = asset('storage/' . $path);
        //     // $imageUrl = url(Storage::url($path));
        //     // $data["thumbnail"] = $imageUrl;

        //     $course = Course::create(array_merge($data, ["thumbnail" => $path], ["instructor_id" => $id]));

        //     return CourseResource::make($course)->additional(["message" => "Course Created Successfully"])->response()->setStatusCode(201);
        // } catch (Exception $e) {
        //     return response()->json([
        //         "error" => $e->getMessage()
        //     ]);
        // }
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
        if ($attributes["thumbnail"]) {
            $attributes = Arr::except($attributes, "thumbnail");
        }
        // need to fill photo update

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
    public function updateThumbnail(Request $request, Course $course)
    {

        $attr = $request->validate([
            'thumbnail' => [
                "required",
                'file',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);



        $image = $attr["thumbnail"];

        $oldPath = str_replace("/", "\\", $course->thumbnail);
        if (File::exists(public_path("storage\\" . $oldPath))) {
            File::delete(public_path("storage\\" . $oldPath));
            return "File deleted successfully!";
        } else {

            $path = $image->storeAs('thumbnails', time() . "$" . auth()->id()  .  Str::snake($course->course_name)  . "." . $image->getClientOriginalExtension(), 'public');
            $course->update(["thumbnail" => $path]);
            return response()->json([
                "message" => "Course thumbnail updated successfully.",
            ], 200);
        }
    }
    public function publish(Request $request, Course $course)
    {


        if (!$course) {
            return response()->json([
                'message' => "Course not found.",

            ], 404);
        }
        try {
            if ($course->is_available === true) {

                $course->update(["is_available" => false]);
                return response()->json([
                    'message' => "Course  unpublished successfully.",

                ], 400);
            }
            $course->update(["is_available" => true]);

            return response()->json([
                "message" => "Course published successfully.",

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed to publish course",
                "error" => $e->getMessage(),
            ], 400);
        }
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
        if (is_("student")) {

            $student =  $user->student;
            $isEnrolled = is_enrolled($student->id, $course->id);
        }

        if ($isEnrolled or Gate::allows("course_details", $course)) {
            $result = Course::with(["lessons" => function ($query) use ($course) {
                $query->where("is_available", true);
            }, "social_link:course_id,facebook,x,phone,telegram,email", "category:id,name", "instructorUser" => function ($query) {
                $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
            }, "category:id,name"])->where("is_available", true)->findOrFail($course->id);
            return CourseResource::make($result)->additional(["message" => "course retrieve successfully🎉"]);
        } else {
            // no account state

            $result = Course::with([
                'lessons' => function ($query) {
                    $query->select("title", "course_id", "id", "lesson_detail")->where("is_available", true);
                },
                "instructorUser" => function ($query) {
                    $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
                }
            ])->where("is_available", true)->findOrFail($course->id);


            return CourseResource::make($result);
        }
    }
    public function requestAdmin(Course $course)
    {
        RequestCreateCourse::dispatch(new CourseCreated($course));
        return response()->json([
            "message" => "Successfully request to publish your course"
        ]);
    }
}
