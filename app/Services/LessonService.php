<?php

namespace App\Services;

use App\Repositories\LessonRepository;

class LessonService
{
    public function __construct(protected LessonRepository $lessonRepo){
    }

    public function all($courseId)
    {
        return $this->lessonRepo->all($courseId);
    }
    public function show($lessonId)
    {
        return $this->lessonRepo->show( $lessonId);
    }
    public function create($data,$courseId)
    {
        return $this->lessonRepo->create($data, $courseId);
    }

    public function update($data,$lessonId)
    {
        return $this->lessonRepo->update($data, $lessonId);
    }

    public function delete($courseId,$lessonId)
    {
        return $this->lessonRepo->delete($courseId, $lessonId);
    }

    public function togglePublish($courseId,$lessonId)
    {
        return $this->lessonRepo->togglePublish($courseId, $lessonId);
    }
}
