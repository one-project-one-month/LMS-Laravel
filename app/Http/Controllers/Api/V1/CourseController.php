<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Models\Course;
use App\Services\CourseService;
use App\Traits\customPaginationFormat;
use App\Traits\ResponseTraits;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    use ResponseTraits, customPaginationFormat;

    public function __construct(protected CourseService $courseService) {}

    /**
     *  Get all courses
     *  get - /api/courses
     */
    //* get all course not include course details 
    public function index(Request $request)
    {
        $result = $this->courseService->getAll($request);

        return $this->successResponse(message: "Course fetched successfully", data: $this->paginateFormat($result));
    }
    public function myCourse(Request $request)
    {

        $user = auth()->user();
        $courses = $user->student->courses()->filter($request)->with("instructorUser", "category")->get();
        // $courses = $user->student->courses()

        // foreach ($courses as $course) {
        //  $user =  $course->instructorUser;
        // $data = [ ...$data , "instructor" => $user ] ;
        // }
        $formatCourses = CourseResource::collection($courses);
        return $this->successResponse(message: "My course fetched successfully", data: $formatCourses);
    }

    /**
     *  store course
     *  post - /api/courses
     *  @param - instructor_id, course_name, thumbnail, type, level, description, duration, original_price, current_price, category_id
     */

    //*  create course auto draft not publish
    public function store(CourseRequest $courseRequest): JsonResponse
    {
        $data = $courseRequest->validated();
        $file = $courseRequest->file("thumbnail");

        $course = $this->courseService->create($data, $file);
        return CourseResource::make($course)->additional(["message" => "Course Created Successfully"])->response()->setStatusCode(201);
    }

    //* update course by instructor with id 
    //! not update publish and draft
    public function update(Request $courseRequest, Course $course)
    {
        $data = request()->all();
        $data = Arr::except($data , "thumbnail");
        $file = $courseRequest->file("thumbnail");
        return response()->json(["message"=>"test", "data"=>$data]);
        $course =  $this->courseService->update( $data , $course->id);


        return CourseResource::make($course)->additional(["message" => "Course update successfully"]);
    }
    //* update thumbnail with id , payload must be file type
    public function updateThumbnail(ImageRequest $request, $courseId): JsonResponse
    {

        $attr = $request->validated();
        $image = $attr["thumbnail"];
        $path =  $this->courseService->updateThumbnail($image, $courseId);
        return $this->successResponse("Course thumbnail updated successfully.", url("/storage/" .  $path));
    }

    //* publish course by admin with id
    public function publish(Request $request, $courseId): JsonResponse
    {

        $attr = $request->validate([
            "is_available" => "boolean"
        ]);
        if (!$attr["is_available"]) {
            return $this->errorResponse(message: "Publish course Failed", status: Response::HTTP_BAD_REQUEST);
        }
        $course =   $this->courseService->publish($attr["is_available"], $courseId);
        return $this->successResponse("Course  publish successfully.");
    }
    //* unpublish course by instructor and admin with id
    public function unpublish(Request $request, $courseId): JsonResponse
    {

        $attr = $request->validate([
            "is_available" => "boolean"
        ]);

        $this->courseService->publish($attr["is_available"], $courseId);
        return $this->successResponse("Course  unpublish successfully.");
    }

    //* delete course by instructor with id
    public function destroy(Course $course): JsonResponse
    {
        $this->courseService->destroy($course->id);
        return $this->successResponse("delete successfully");
    }

    //* get course details base on enrolled or not , instructor ,admin all access
    public function show($courseId)
    {
        $course = $this->courseService->getById($courseId);
        return CourseResource::make($course)->additional(["message" => "course retrieve successfully🎉"]);
    }
    public function normal(Course $course)
    {
        return CourseResource::make($course)->additional(["message" => "course retrieve successfully🎉"]);
    }
    //* publish request to admin 
    public function request(
        Course $course
    ) {

        $this->courseService->request($course->id);
        return $this->successResponse("Successfully request to publish your course");
    }

    //set student for complement 
    //
    public function complete(Request $request,  $courseId): JsonResponse
    {
        $attributes = $request->validate([
            "user_id" => "required|exists:students,id",
        ]);
        $result =   $this->courseService->complete($attributes["user_id"], $courseId);

        if ($result) {
            return $this->successResponse("Set Completion to student successfully");
        } else {
            return $this->errorResponse("Student is not enrolled the course!", "", 400);
        }
    }
}
