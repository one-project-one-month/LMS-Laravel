<?php

use App\Models\Role;
use App\Models\Student;

function get_role_id($role)
{
    $role_id = Role::where("role", $role)->first()->id;
    return $role_id;
}
function is_($role)
{
    $user = auth()->user(); // Get the authenticated user

    return $user && $user->role_id === get_role_id($role);
}
function is_enrolled($studentId, $courseId)
{
    $student = Student::find($studentId);


    return $student->courses->contains("id", $courseId);
}
