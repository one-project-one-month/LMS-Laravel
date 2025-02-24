<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorLoginRequest;
use App\Http\Requests\InstructorRegisterRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{


    public function __invoke(RegisterRequest $request)
    {
        if ($request->input("role") === "instructor") {
            $responseJson =   app(InstructorAuthController::class)->register($request);

            return $responseJson;
        } elseif ($request->input("role") === "student") {
            $responseJson =   app(StudentAuthController::class)->register($request);

            return $responseJson;
        }
        return response()->json([
            "message" => "helo",
            "request" => $request->all()
        ]);
    }
}


    // try {
    //     $user = User::query()->create([
    //         'username' => $request->username,
    //         'email' => $request->email,
    //         'password' => $request->password
    //     ]);


    //     $instructor = Instructor::query()->create([
    //         'user_id' => $user->id,
    //         'nrc' => $request->nrc,
    //         'edu_background' => $request->edu_background
    //     ]);

    //     $token = JWTAuth::fromUser($user);

    //     return response()->json([
    //         'message' => 'Instructor registered successfully',
    //         'data' => [
    //             'instructor' => $instructor,
    //             'token' => $token
    //         ]
    //     ]);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'message' => 'Registration failed',
    //         'error' => $e->getMessage(),
    //     ], 500);
    // }
    // }
