<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegisterRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentRegisterController extends Controller
{
    /**
     * Register a new student
     * 
     * @param StudentRegisterRequest $request
     * @return JsonResponse
     */
    public function __invoke(StudentRegisterRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Student registered successfully',
                'data' => [
                    'student' => $user,
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
