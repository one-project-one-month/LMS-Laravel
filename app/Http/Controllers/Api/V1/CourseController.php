<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Mail\CourseCreated;
use App\Models\Course;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{

    // public function test()
    // {


    //     if (Gate::allows("createCourse",Course::class)) {
    //         return "allows";
    //     } else {
    //         return "no allows";
    //     }
    // }
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

    //* done
    public function store(CourseRequest $courseRequest)
    {
        try {
            $data = Arr::except($courseRequest->validated(), ["thumbnail"]);
            $image = Arr::only($courseRequest->validated(), ['thumbnail']);
            $user = JWTAuth::parseToken()->authenticate();
            $id = $user->instructor->id;

            // Get the uploaded file from the 'thumbnail' key
            $file = $image['thumbnail'];
            try {
                $path = $file->storeAs('thumbnails', time() . "$" . $user->id  .  Str::snake($data["course_name"])  . "." . $file->getClientOriginalExtension(), 'public');
            } catch (Exception $e) {
                return response()->json([
                    "error" => $e->getMessage()
                ]);
            }
            // $imageUrl = asset('storage/' . $path);
            // $imageUrl = url(Storage::url($path));
            // $data["thumbnail"] = $imageUrl;

            $course = Course::create(array_merge($data, ["thumbnail" => $path], ["instructor_id" => $id]));

            return CourseResource::make($course)->additional(["message" => "Course Created Successfully"])->response()->setStatusCode(201);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Creating Course Failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     *  store courese
     *  put - /api/courses/:id
     *  @param id
     *  @param request
     */
    //*done
    public function update(CourseRequest $courseRequest, Course $course)
    {
        try {
            $attributes = $courseRequest->validated();
            //! disable photo update
            if (key_exists("thumbnail", $attributes)) {

                $attributes = Arr::except($attributes, "thumbnail");
            }

            $course->update($attributes);



            return CourseResource::make($course)->additional(["message" => "Course update successfully"]);
        } catch (Exception $e) {
            return response()->json([
                "message" => "something was wrong",
                "error" => $e->getMessage()
            ]);
        }
    }
    //*done
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
        }

        $path = $image->storeAs('thumbnails', time() . "$" . auth()->id()  .  Str::snake($course->course_name)  . "." . $image->getClientOriginalExtension(), 'public');
        $course->update(["thumbnail" => $path]);
        return response()->json([
            "message" => "Course thumbnail updated successfully.",
        ], 200);
    }
    //*done

    public function publish(Request $request, Course $course)
    {
        $attr = $request->validate([
            "is_available" => "boolean"
        ]);
        try {

            if ($attr["is_available"]) {
                $course->update(["is_available" => $attr["is_available"]]);

                return response()->json([
                    'message' => "Course  publish successfully.",

                ], 200);
            } else {
                throw new BadRequestException("Publish request must be true");
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed to publish course",
                "error" => $e->getMessage(),
            ], 400);
        }
    }
    //*done
    public function unpublish(Request $request, Course $course)
    {
        $attr = $request->validate([
            "is_available" => "boolean"
        ]);
        return $attr["is_available"];
        try {

            if (!$attr["is_available"]) {
                $course->update(["is_available" => $attr["is_available"]]);

                return response()->json([
                    'message' => "Course  unpublish successfully.",

                ], 200);
            } else {
                throw new BadRequestException("Unpublish request must be false");
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed to unpublish course",
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
    //*done
    public function destroy(Course $course)
    {
        try {

            $course->delete();
            return response()->json([
                "message" => "delete successfully",

            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                "message" => "Failed to delete course",
                "error" => $e->getMessage()

            ], 500);
        }
    }

    //*done
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
                },
                "category:id,name"
            ])->where("is_available", true)->findOrFail($course->id);

            return CourseResource::make($result)->additional(["message" => "course retrieve successfully🎉"]);
        }
    }

    //*done
    public function requestAdmin(Course $course)
    {
        RequestCreateCourse::dispatch($course);
        return response()->json([
            "message" => "Successfully request to publish your course"
        ]);
    }
}
