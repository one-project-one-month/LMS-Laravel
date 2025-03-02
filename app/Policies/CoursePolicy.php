<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
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


    public function view(User $user, Course $course): bool
    {
        return true;
    }
    public function course_details(User $user, Course $course): bool
    {
        return ($user->role_id === get_role_id("instructor") && $user->instructor->id === $course->instructor_id) or $user->role_id === get_role_id("admin");
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role_id === 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        if (is_("instructor") or is_("admin")) {
            return ($user->instructor->id === $course->instructor_id) or is_("admin");
        } else {
            return false;
        }
    }
    public function complete(User $user, Course $course): bool
    {



        return ($user->instructor->id === $course->instructor_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->instructor->id === $course->instructor_id  or $user->id === 1;
    }
}
