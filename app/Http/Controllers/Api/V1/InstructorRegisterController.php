<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorRegisterRequest;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class InstructorRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(InstructorRegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $instructor = Instructor::create([
                'user_id' => $user->id,
                'nrc' => $request->nrc,
                'edu_background' => $request->edu_background,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Instructor registered successfully',
                'data' => [
                    'instructor' => array_merge($user->toArray(), $instructor->toArray()),
                    'token' => $token,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
