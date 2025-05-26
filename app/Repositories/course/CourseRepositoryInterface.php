<?php

namespace App\Repositories\course;

interface CourseRepositoryInterface
{
    public function index($request , $sortBy , $sortDirection , $limit);
    public function store($data);
    public function update($data , $id);
    public function updatethumbnailPath($path , $id) ;
   public function getCourseDetails($canAccessCourse, $id);

    public function destroy($id);
    public function show($id);
    public function complete($studentId , $courseId);
}
