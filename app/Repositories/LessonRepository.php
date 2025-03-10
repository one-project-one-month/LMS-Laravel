<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Lesson;
use App\Interfaces\LessonInterface;
use ErrorException;

class LessonRepository implements LessonInterface
{
    public function all(int $courseID): \Illuminate\Database\Eloquent\Collection
    {
        $course = Course::with('lessons')->find($courseID);

        return $course->lessons;
    }

    public function show(int $courseId,int $lessonId) : Lesson
    {
        $course = Course::find($courseId);
        $lesson = Lesson::find($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        return $lesson;
    }

    public function create(array $data,int $courseId) : Lesson
    {
        $course = Course::findOrFail($courseId);

        return $course->lessons()->create($data);
    }

    public function update(array $data, int $courseId, int $lessonId): Lesson
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        $lesson->update($data);
        return $lesson->fresh();
    }

    public function delete(int $courseId, int $lessonId): bool
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        $this->validateLessonBelongsToCourse($course, $lesson);

        return $lesson->delete();
    }

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

    protected function validateLessonBelongsToCourse(Course $course, Lesson $lesson)
    {
        if ($course->id != $lesson->course_id) {
            abort(response()->json([
                'error' => "Lesson not found in $course->course_name.",
            ], 404));
        }
    }
}
