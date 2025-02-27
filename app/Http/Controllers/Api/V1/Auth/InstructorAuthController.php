<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorLoginRequest;
use App\Http\Requests\InstructorRegisterRequest;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class InstructorAuthController extends Controller
{


    public function register($request)
    {

        $data = $request->validated();
        $userData = Arr::except($data, ["nrc", "edu_background", "role"]);
        $instructorData = Arr::only($data, ["nrc", "edu_background"]);

        $instructor_role_id = Role::query()->where("role", "instructor")->first()->id;

        try {
            $user = User::query()->create(array_merge($userData, ["role_id" => $instructor_role_id]));


            $instructor =   $user->instructor()->create($instructorData);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Instructor registered successfully',
                'data' => [
                    'instructor' => $user,
                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
