<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Lesson;
use App\Interfaces\LessonInterface;
use Illuminate\Support\Facades\Storage;

class LessonRepository implements LessonInterface
{
    // get all lesson
    public function all(int $courseID): \Illuminate\Database\Eloquent\Collection
    {
        $course = Course::with('lessons')->find($courseID);

        return $course->lessons;
    }

    // lesson detail
    public function show(int $courseId, int $lessonId): Lesson
    {
        $course = Course::find($courseId);
        $lesson = Lesson::find($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        return $lesson;
    }

    // create lesson
    public function create(array $data, int $courseId): Lesson
    {
        $course = Course::findOrFail($courseId);

        return $course->lessons()->create($data);
    }

    // update lesson
    public function update(array $data, int $courseId, int $lessonId): Lesson
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        if (isset($data["video_url"]) && Storage::disk('public')->exists($lesson->video_url)) {
            Storage::disk('public')->delete($lesson->video_url);
        }

        $this->validateLessonBelongsToCourse($course, $lesson);

        $lesson->update($data);
        return $lesson->fresh();
    }

    // lesson delete
    public function delete(int $courseId, int $lessonId): bool
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        return $lesson->delete();
    }

    // toggle public
    public function togglePublish(int $courseId, int $lessonId): ?Lesson
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        $lesson->update([
            "is_available" => !(bool) $lesson->is_available
        ]);

        return $lesson->fresh();
    }

    // validate lesson belongs to course
    protected function validateLessonBelongsToCourse(Course $course, Lesson $lesson)
    {
        if ($course->id != $lesson->course_id) {
            abort(response()->json([
                'error' => "Lesson not found in $course->course_name.",
            ], 404));
        }
    }
}
