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

    public function show($courseId,$lessonId)
    {
        return $this->lessonRepo->show($courseId, $lessonId);
    }
    public function create($data,$courseId)
    {
        return $this->lessonRepo->create($data, $courseId);
    }

    public function update($data,$courseId,$lessonId)
    {
        return $this->lessonRepo->update($data, $courseId, $lessonId);
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
