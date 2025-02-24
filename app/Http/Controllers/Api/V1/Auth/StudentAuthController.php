<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentLoginRequest;
use App\Http\Requests\StudentRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentAuthController extends Controller
{
    public function login(StudentLoginRequest $request)
    {

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
            ]
        ]);
    }

    public function register(StudentRegisterRequest $request): JsonResponse
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

            return response()->json([
                'message' => 'Student registered successfully',
                'data' => [
                    'student' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json([
                'message' => 'Logout successful'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
