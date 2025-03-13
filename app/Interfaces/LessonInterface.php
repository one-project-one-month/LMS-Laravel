<?php

namespace App\Interfaces;

use App\Models\Lesson;

interface LessonInterface
{
    public function all(int $courseID): \Illuminate\Database\Eloquent\Collection;
    public function show(int $courseId,int $lessonId): ?Lesson;

    public function create(array $data,int $courseId): ?Lesson;

    public function update(array $data, int $courseId, int $lessonId): Lesson;

    public function delete(int $courseId, int $lessonId): bool;

    public function togglePublish(int $courseId, int $lessonId): ?Lesson;

}
