<?php

use App\Models\Role;
use App\Models\Student;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

function get_role_id($role)
{
    $role_id = Role::where("role", $role)->first()->id;
    return $role_id;
}
function get_role_name ($id){
    $role_name = Role::findorFail($id)->role;
    return $role_name;
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

function successResponse(string $message,$data = null,int $status = HttpFoundationResponse::HTTP_OK): JsonResponse
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
 function generateRefreshToken(){
    $refresh_token = Str::random(16);
return $refresh_token;
}
