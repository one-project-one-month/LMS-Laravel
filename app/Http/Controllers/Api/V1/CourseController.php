<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Mail\CourseCreated;
use App\Models\Course;
use App\Repositories\course\CourseRepositoryInterface;
use App\Services\CourseService;
use App\Traits\ResponseTraits;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseController extends Controller
{
    use ResponseTraits;

    public function __construct(protected CourseService $courseService) {}





    /**
     *  Get all courses
     *  get - /api/courses
     */
    //* done 
    public function index(Request $request)
    {
        $result = $this->courseService->getAll($request);

        return  new CourseCollection($result);
    }

    /**
     *  store courese
     *  post - /api/courses
     *  @param - instructor_id, course_name, thumbnail, type, level, description, duration, original_price, current_price, category_id
     */

    //* done
    public function store(CourseRequest $courseRequest)
    {
        $data = $courseRequest->validated();
        $course = $this->courseService->create($data);
        return CourseResource::make($course)->additional(["message" => "Course Created Successfully"])->response()->setStatusCode(201);

        //    catch (Exception $e) {
        //         return $this->errorResponse(message: "Creating Course Failed!", error: $e->getMessage());
        //     }
    }

    /**
     *  store courese
     *  put - /api/courses/:id
     *  @param id
     *  @param request
     */
    //*done
    public function update(CourseRequest $courseRequest, $courseId)
    {
        $course =  $this->courseService->update($courseRequest->validated(), $courseId);


        return CourseResource::make($course)->additional(["message" => "Course update successfully"]);
        // } catch (Exception $e) {
        //     return response()->json([
        //         "message" => "something was wrong",
        //         "error" => $e->getMessage()
        //     ]);
        // }
    }
    //*done
    public function updateThumbnail(Request $request, $courseId)
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
        $course = $this->courseService->updateThumbnail($image, $courseId);
        return $this->successResponse("Course thumbnail updated successfully.");
    }
    //*done

    public function publish(Request $request, $courseId)
    {
        try {
            $attr = $request->validate([
                "is_available" => "boolean"
            ]);
            $course =   $this->courseService->publish($attr["is_available"], $courseId);




            return $this->successResponse("Course  publish successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse(
                "failed to publish course",
                "error",
                $e->getMessage(),
                400
            );
        }
    }
    //*done
    public function unpublish(Request $request, $courseId)
    {
        try {
            $attr = $request->validate([
                "is_available" => "boolean"
            ]);
            $course =   $this->courseService->publish($attr["is_available"], $courseId);




            return $this->successResponse("Course  unpublish successfully.");
        } catch (\Exception $e) {
            return $this->errorResponse(
                "failed to unpublish course",
                "error",
                $e->getMessage(),
                400
            );
        }
    }
    /**
     *  delete course
     *  delete - /api/courses/:id
     * @param id
     * @param request
     */
    //*done
    public function destroy($courseId)
    {


        $this->courseService->destroy($courseId);
        return $this->successResponse("delete successfully", status: 204);
    }

    //*done
    public function show($courseId)
    {
        $course = $this->courseService->getById($courseId);
        return CourseResource::make($course)->additional(["message" => "course retrieve successfully🎉"]);
    }
    //*done
    public function requestAdmin($id)
    {

        return $this->successResponse("Successfully request to publish your course");
    }
    //set student for complement 
    //* all done
    public function complete(Request $request,  $courseId)
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
