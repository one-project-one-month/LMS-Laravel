<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Services\CourseService;
use App\Traits\customPaginationFormat;
use App\Traits\ResponseTraits;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    use ResponseTraits,customPaginationFormat;

    public function __construct(protected CourseService $courseService) {}

    /**
     *  Get all courses
     *  get - /api/courses
     */
    //* get all course not include course details 
    public function index(Request $request)
    {
        $result = $this->courseService->getAll($request);
    
        return $this->successResponse(message:"Course fetched successfully" , data:$this->paginateFormat($result));
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
        $course = $this->courseService->create($data);
        return CourseResource::make($course)->additional(["message" => "Course Created Successfully"])->response()->setStatusCode(201);
    }

    //* update course by instructor with id 
    //! not update publish and draft
    public function update(CourseRequest $courseRequest, $courseId): CourseResource
    {
        $course =  $this->courseService->update($courseRequest->validated(), $courseId);


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
    public function destroy($courseId): JsonResponse
    {
        $this->courseService->destroy($courseId);
        return $this->successResponse("delete successfully", status: Response::HTTP_NO_CONTENT);
    }

    //* get course details base on enrolled or not , instructor ,admin all access
    public function show($courseId): CourseResource
    {
        $course = $this->courseService->getById($courseId);
        return CourseResource::make($course)->additional(["message" => "course retrieve successfully🎉"]);
    }
    //* publish request to admin 
    public function request($id)
    {
        $this->courseService->request($id);
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
