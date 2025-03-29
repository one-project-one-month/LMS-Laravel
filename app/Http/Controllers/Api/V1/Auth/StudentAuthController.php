<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentLoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\StudentRegisterRequest;

class StudentAuthController extends Controller
{


    public function register($request)
    {
        //need to wrap DB:transistion()
        try {
            $user = User::query()->create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
            ]);
            $user->student()->create(["user_id" => $user->id]);

            $token = JWTAuth::fromUser($user);

            return [$token, $user->refresh_token];


            // return response()->json([
            //     'message' => 'Student registered successfully',
            //     'data' => [
            //         'student' => $user,
            //         'token' => $token
            //     ]
            // ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
