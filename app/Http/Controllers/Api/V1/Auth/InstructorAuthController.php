<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Instructor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\InstructorLoginRequest;
use App\Http\Requests\InstructorRegisterRequest;
use App\Traits\ResponseTraits;
use Symfony\Component\HttpFoundation\Response;

class InstructorAuthController extends Controller
{
    use ResponseTraits;

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
            return [$token, $user->refreshToken->refresh_token];
        } catch (\Exception $e) {
            return $this->errorResponse(message: 'Registration failed', error: $e->getMessage(), status: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
