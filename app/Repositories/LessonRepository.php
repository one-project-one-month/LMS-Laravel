<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class LessonRepository
{
    // get all lesson
    public function all(int $courseID): \Illuminate\Database\Eloquent\Collection
    {
        $course = Course::with('lessons')->find($courseID);

        return $course->lessons;
    }

    // lesson detail
    public function show( int $lessonId): Lesson
    {
        $lesson = Lesson::findOrFail($lessonId);

        return $lesson;
    }

    // create lesson
    public function create(array $data, int $courseId): Lesson
    {
        $course = Course::findOrFail($courseId);

        return $course->lessons()->create($data);
    }

    // update lesson
    public function update(array $data, int $lessonId): Lesson
    {
    
        $lesson = Lesson::findOrFail($lessonId);

        // if (isset($data["video_url"]) && Storage::disk('public')->exists($lesson->video_url)) {
        //     Storage::disk('public')->delete($lesson->video_url);
        // }

        $lesson->update($data);
        return $lesson->fresh();
    }

    // lesson delete
    public function delete(int $courseId, int $lessonId): bool
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($lessonId);

        return $lesson->delete();
    }

    // toggle public
    public function togglePublish(int $courseId, int $lessonId): ?Lesson
    {
        $lesson = Lesson::findOrFail($lessonId);
        $lesson->update([
            "is_available" => !(bool) $lesson->is_available
        ]);

        return $lesson->fresh();
    }

 
}
