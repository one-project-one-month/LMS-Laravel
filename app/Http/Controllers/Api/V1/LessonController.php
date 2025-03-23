<?php

namespace App\Http\Controllers\Api\V1;

use App\Interfaces\LessonInterface;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Resources\LessonResource;
use App\Http\Resources\LessonCollection;
use App\Http\Requests\LessonUploadVideoRequest;
use App\Repositories\LessonRepository;
use App\Services\LessonService;
use Illuminate\Http\Exceptions\HttpResponseException;

class LessonController extends Controller
{
    public function __construct(protected LessonService $lessonService){
    }
    /**
     *  Get all lessons
     *  get - /api/courses/:id/lessons
     *  @param ( course_id )
     */
    public function index($courseId)
    {
        $lessons = $this->lessonService->all($courseId);

        if ($lessons->isEmpty()) {
            return errorResponse("Lessons not found.", 400);
        }

        return successResponse("Lessons retrieved successfully.", new LessonCollection($lessons));
    }

    /**
     *  get lesson
     *  get - /api/courses/:id/lessons/:id
     *  @param ( course_id , lesson_id )
     */
    public function show(Course $course,Lesson $lesson)
    {
        $lesson = $this->lessonService->show($course->id, $lesson->id);

        return successResponse("Lesson retrieved successfully.", new LessonResource($lesson));
    }

    /**
     *  store lesson
     *  post - /api/courses/:id/lessons/
     *  @param - title, lesson_detail, is_available, ( video_url - get from uploadUrl Api )
     */
    public function store(LessonRequest $lessonRequest,int $courseId)
    {
        $attributes = $lessonRequest->validated();

        $lesson = $this->lessonService->create($attributes,$courseId);

        return successResponse("Lesson created successfully.", new LessonResource($lesson),201);
    }

    /**
     *  update lesson
     *  put - /api/courses/:id/lessons/:id
     * @param ( course_id , lesson_id )
     * @param request,( course_id - optional )
     */

    public function update(LessonRequest $lessonRequest, Course $course, Lesson $lesson)
    {
        $attributes = $lessonRequest->validated();

        $lesson = $this->lessonService->update($attributes,$course->id,$lesson->id);

        return successResponse("Lesson updated successfully.", new LessonResource($lesson));
    }

    /**
     *  delete lesson
     *  delete - /api/courses/:id/lessons/:id
     * @param ( course_id , lesson_id )
     */
    public function destroy(Course $course,Lesson $lesson)
    {
        $this->lessonService->delete($course->id, $lesson->id);

        return successResponse("Lesson deleted successfully.");
    }

    // toggle publish
    // patch - api/v1/courses/:id/lessons/:id/togglePublish
    public function publish(Course $course,Lesson $lesson)
    {
        $lesson = $this->lessonService->togglePublish($course->id, $lesson->id);

        return successResponse("$lesson->title publish status has been changed.", new LessonResource($lesson));
    }
}
