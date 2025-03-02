<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        $courserId = $lesson->course_id;
        return $user->role->name === "admin" || $user->role->name === "instructor" || $user->student->courses->contains($courserId);
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name === "instructor";
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->role->name === "instructor" && $user->instructor->id === $lesson->course->instructor_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->role->name === "instructor" && $user->instructor->id === $lesson->course->instructor_id;
    }

    public function uploadVideoUrl(User $user, Lesson $lesson): bool
    {

        return $user->role->name === "instructor" && $user->instructor->id === $lesson->course->instructor_id;
    }
}
