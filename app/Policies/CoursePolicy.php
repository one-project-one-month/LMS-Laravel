<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

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






    /**
     * Determine whether the user can create models.
     */


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
    public function course_details(User $user, Course $course): bool
    {
        if (is_("instructor") or is_("admin")) {
            return ($user->instructor->id === $course->instructor_id) or is_("admin");
        } else {
            return false;
        }
    }
    public function completeCourse(User $user, Course $course): bool
    {


        if (is_("instructor")) {

            return ($user->instructor->id === $course->instructor_id);
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool

    {
        if (is_("instructor") or is_("admin")) {
            return ($user->instructor->id === $course->instructor_id) or is_("admin");
        } else {
            return false;
        }
    }
}
