<?php

namespace App\Services;

use App\Jobs\RequestCreateCourse;
use App\Repositories\course\CourseRepositoryInterface;
use App\Traits\ResponseTraits;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\throwException;

class CourseService
{
    use ResponseTraits;
    public function __construct(protected CourseRepositoryInterface $courseRepository) {}
    public function getAll($request)
    {
        $validSortColumns = ['id', 'current_price', 'created_at'];
        $sortBy = in_array($request->input("sort_by"), $validSortColumns, true) ? $request->input("sort_by") : "id";
        $sortDirection =   $request->input("sort_direction") ?? "desc";
        $limit = $request->input("limit", 10);
        $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? $limit : 10;
        try {
            $result =  $this->courseRepository->index($request->all(), $sortBy, $sortDirection, $limit);
            return $result;
        } catch (Exception $e) {

            return $this->errorResponse(message: "Failed to load courses", error: $e->getMessage());
        }
    }
    public function getById($id)
    {
        $token = request()->bearerToken();

        $course = $this->courseRepository->show($id);
        $isEnrolled = false;
        $canAccessCourse = false;
        if ($token) {

            $user = JWTAuth::parseToken()->authenticate();
            if (is_("student")) {

                $student =  $user->student;
                $isEnrolled = is_enrolled($student->id, $course->id);
            }
        }



        if ($isEnrolled or Gate::allows("course_details", $course)) {
            $canAccessCourse = true;
        } else {
            // no account saturation
            $canAccessCourse = false;
        }
        $result = $this->courseRepository->getCourseDetails($canAccessCourse, $id);
  
        return $result;
    }
    public function create($data)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $id = $user->instructor->id;

        // Get the uploaded file from the 'thumbnail' key
        $file = $data['thumbnail'];
        $path = $this->storeThumbnail($file, $data["course_name"]);
        if ($path) {
            $data['thumbnail'] = $path;
            $data = array_merge($data, ["instructor_id" => $id]);
            $course = $this->courseRepository->store($data);
            return $course;
        }
        return throw new Exception("Failed to store image");
    }

    public function updateThumbnail($image, $id)
    {
        $course = $this->courseRepository->show($id);

        $oldPath = str_replace("/", "\\", $course->thumbnail);
        if (File::exists(public_path("storage\\" . $oldPath))) {
            File::delete(public_path("storage\\" . $oldPath));
        }

        $path = $this->storeThumbnail($image, $course->course_name);

        $course = $this->courseRepository->update($path, $id);
        return $course->thumbnail;
    }

    public function update($data, $id)
    {
        //! disable photo update
        if (key_exists("thumbnail", $data)) {
            $data = Arr::except($data, "thumbnail");
        }
        $course = $this->courseRepository->update($data, $id);
        return $course;
    }


    public function publish($is_available, $id)
    {
        $data = ["is_available" => $is_available];
        if (!$is_available) {
            throw new BadRequestException("Publish request must be true");
        }
        $course =  $this->courseRepository->update($data,  $id);
        return $course;
    }
    public function unpublish($is_available, $id)
    {
        $data = ["is_available" => $is_available];
        if ($is_available) {
            throw new BadRequestException("Unpublish request must be false");
        }
        $course =  $this->courseRepository->update($data,  $id);
        return $course;
    }
    public function destroy($id)
    {
        $this->courseRepository->destroy($id);
    }

    public function request($id)
    {
        $course  = $this->courseRepository->show($id);
        RequestCreateCourse::dispatch($course);
    }
    public function complete($studentId, $courseId)
    {
        $course = $this->courseRepository->show($courseId);
        if (is_enrolled($studentId, $courseId)) {
            if (!Gate::allows("completeCourse", $course)) {

                return false;
            }
            $this->courseRepository->complete($studentId, $courseId);
            return true;
        } else {
            return false;
        }
    }
    public function  storeThumbnail($image, $course_name)
    {
        $path = $image->storeAs('thumbnails', time() . "$" . auth()->id()  .  Str::snake($course_name)  . "." . $image->getClientOriginalExtension(), 'public');
        return $path;
    }
}
