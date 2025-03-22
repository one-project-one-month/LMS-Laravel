<?php

use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

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

function successResponse(string $message,$data = null,int $status = 200): JsonResponse
{
    $response = [
        "message" => $message,
    ];

    if ($data != null) {
        $response['data'] = $data;
    }

    return response()->json($response, $status);
}

function errorResponse(string $message, int $status = 404): JsonResponse
{
    return response()->json([
        "message" => $message
    ], $status);
}
