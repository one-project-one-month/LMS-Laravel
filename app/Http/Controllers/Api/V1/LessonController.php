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
use Illuminate\Http\Exceptions\HttpResponseException;

class LessonController extends Controller
{
    public function __construct(protected LessonInterface $lessonInterface)
    {

    }
    /**
     *  Get all lessons
     *  get - /api/courses/:id/lessons
     *  @param ( course_id )
     */
    public function index(int $id)
    {
        $lessons = $this->lessonInterface->all($id);

        if ($lessons->isEmpty()) {
            return errorResponse("Lessons not found.", 400);
        }

        return successResponse("Lessons retrieved successfully.", new LessonCollection($lessons), 200);
    }

    /**
     *  get lesson
     *  get - /api/courses/:id/lessons/:id
     *  @param ( course_id , lesson_id )
     */
    public function show(Course $course,Lesson $lesson)
    {
        $lesson = $this->lessonInterface->show($course->id, $lesson->id);

        return successResponse("Lesson retrieved successfully.", new LessonResource($lesson));
    }

    /**
     *  store lesson
     *  post - /api/courses/:id/lessons/
     *  @param - title, lesson_detail, is_available, ( video_url - get from uploadUrl Api )
     */
    public function store(LessonRequest $lessonRequest,int $id)
    {
        $attributes = $lessonRequest->validated();

        $lesson = $this->lessonInterface->create($attributes,$id);

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

        $lesson = $this->lessonInterface->update($attributes,$course->id,$lesson->id);

        return successResponse("Lesson updated successfully.", new LessonResource($lesson));
    }

    /**
     *  delete lesson
     *  delete - /api/courses/:id/lessons/:id
     * @param ( course_id , lesson_id )
     */
    public function destroy(Course $course,Lesson $lesson)
    {
        $this->lessonInterface->delete($course->id, $lesson->id);

        return successResponse("Lesson deleted successfully.");
    }

    // toggle publish
    // patch - api/v1/courses/:id/lessons/:id/togglePublish
    public function publish(Course $course,Lesson $lesson)
    {
        $lesson = $this->lessonInterface->togglePublish($course->id, $lesson->id);

        return successResponse("$lesson->title publish status has been changed.", new LessonResource($lesson));
    }

    /**
     *  video upload
     *  post - /api/lessons/uploadUrl
     * @param ( video ) type - mp4, mov, avi, wmv, flv, or webm.
     */

    public function uploadUrl(LessonUploadVideoRequest $request)
    {
        $video = $request->file('video');

        $pathname = time() . '_' . uniqid() . '.' . $video->getClientOriginalExtension();
        $path = $video->storeAs('lesson-videos', $pathname, 'public');

        return response()->json([
            'message' => 'Video uploaded successfully',
            'path' => $path,
        ], 200);
    }
}
