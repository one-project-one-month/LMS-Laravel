<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LessonCollection;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Requests\LessonUploadVideoRequest;

class LessonController extends Controller
{
    /**
     *  Get all lessons
     *  get - /api/courses/:id/lessons
     *  @param ( course_id )
     */
    public function index(Course $course)
    {
        $lessons = $course->lessons;

        if (!$lessons) {
            return response()->json([
                "message" => "Lessons not found."
            ],404);
        }

        return response()->json([
            "message" => "Lessons retrieved successfully.",
            "datas" => [
                'lessons' => new LessonCollection($lessons)
            ],
        ], 200);
    }

    /**
     *  get lesson
     *  get - /api/courses/:id/lessons/:id
     *  @param ( course_id , lesson_id )
     */

    public function show($id)
    {
        $lesson = Lesson::find($id);




        if ($course->id !== $lesson->course_id) {

            return response()->json([
                'message' => "Lesson not found in the $course->course_name course"
            ],404);
        }

        return response()->json([
            'message' => "Lesson retrieved successfully.",
            'data' => [
                "lesson" => new LessonResource($lesson)
            ],
            'status' => 200
        ], 200);
    }

    /**
     *  store lesson
     *  post - /api/courses/:id/lessons/
     *  @param - title, lesson_detail, is_available, ( video_url - get from uploadUrl Api )
     */
    public function store(LessonRequest $lessonRequest,Course $course)
    {
        $validated = $lessonRequest->validated();

        $lesson = $course->lessons()->create($validated);

        return response()->json([
            'message' => 'Lesson created successfully.',
            'data' => [
                'lesson' => new LessonResource($lesson)
            ],
        ], 201);
    }

    /**
     *  update lesson
     *  put - /api/courses/:id/lessons/:id
     * @param ( course_id , lesson_id )
     * @param request,( course_id - optional )
     */

    public function update(LessonRequest $lessonRequest,Course $course,Lesson $lesson)

    {
        if ($course->id !== $lesson->course_id) {
            return response()->json([
                'message' => "Lesson not found in the $course->course_name course"
            ], 404);
        }
        $validated = $lessonRequest->validated();

        $lesson->update($validated);

        return response()->json([
            "message" => "update successfully",
            "data" => [
                'lesson' => new LessonResource($lesson)
            ],


        ], 200);

    }

    public function publish(Request $request, Lesson $lesson)
    {

        if (!$lesson) {
            return response()->json([
                'message' => "Lesson not found.",

            ], 404);
        }
        try {
            if ($lesson->is_available === true) {

                $lesson->update(["is_available" => false]);
                return response()->json([
                    'message' => "Lesson  unpublished successfully.",

                ], 400);
            }
            $lesson->update(["is_available" => true]);

            return response()->json([
                "message" => "Lesson published successfully.",

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "failed to publish course",
                "error" => $e->getMessage(),
            ], 400);
        }
    }
    /**
     *  delete lesson
     *  delete - /api/courses/:id/lessons/:id
     * @param ( course_id , lesson_id )
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        if ($course->id !== $lesson->course_id) {
            return response()->json([
                'message' => "Lesson not found in the $course->course_name course"
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            "message" => "Lesson delete successfully.",

        ], 200);

    }

    /**
     *  video upload
     *  post - /api/lessons/uploadUrl
     * @param ( video ) type - mp4, mov, avi, wmv, flv, or webm.
     */

    public function uploadUrl(LessonUploadVideoRequest $uploadVideoRequest)
    {
        $video = $uploadVideoRequest->file('video');
        $path = $video->store('lesson-videos', 'public');

        return response()->json([
            'message' => 'Video uploaded successfully',
            'path' => $path,
            'status' => 200
        ], 200);
    }
}
