<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Requests\LessonUploadVideoRequest;

class LessonController extends Controller
{
    /**
     *  Get all lessons
     *  get - /api/lessons
     */
    public function index()
    {
        $lessons = Lesson::latest()->get();

        if (!$lessons) {
            return response()->json([
                "message" => "Lesson not found."
            ]);
        }

        return response()->json([
            "message" => "Lessons retrieved successfully.",
            "datas" => [
                'lessons' => $lessons
            ],
            "status" => 200
        ], 200);
    }

    /**
     *  get lesson
     *  get - /api/lessons/:id
     *  @param id
     */
    public function show($id){
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "message" => "Lesson not found.",
                "status" => 404
            ], 404);
        }

        return response()->json([
            'message' => "Lesson retrieved successfully.",
            'data' => [
                "lesson" => $lesson
            ],
            'status' => 200
        ],200);
    }

    /**
     *  store lesson
     *  post - /api/lessons
     *  @param - course_id, title, lesson_detail, is_available , ( video_url - get from uploadUrl Api )
     */
    public function store(LessonRequest $lessonRequest)
    {
        $attributes = $lessonRequest->validated();

        $lesson = Lesson::create($attributes);

        return response()->json([
            'message' => 'Lesson created successfully.',
            'data' => [
                'lesson' => $lesson
            ],
            'status' => 201
        ], 201);
    }

    /**
     *  update lesson
     *  put - /api/lessons/:id
     * @param id
     * @param request
     */
    public function update(LessonRequest $lessonRequest,$id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "message" => "Lesson not found.",
                "status" => 404
            ], 404);
        }

        $attributes = $lessonRequest->validated();

        $lesson->update($attributes);

        return response()->json([
            "message" => "",
            "data" => [
                'lesson' => $lesson
            ],
            "status" => 200
        ],200);
    }

    /**
     *  delete lesson
     *  delete - /api/lessons/:id
     * @param id
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "message" => "Lesson not found.",
                "status" => 404
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            "message" => "Lesson delete successfully.",
            "status" => 200
        ],200);
    }

    /**
     *  video upload
     *  post - /api/lessons/uploadUrl
     * @param video type - mp4, mov, avi, wmv, flv, or webm.
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
