<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRegisterRequest $studentRegisterRequest)
    {
        try {
            $user = User::create([
                'username' => $studentRegisterRequest->username,
                'email' => $studentRegisterRequest->email,
                'password' => $studentRegisterRequest->password,
            ]);
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'Student Registered SuccessFully',
                'data' => [
                    'student' => $user,
                    'token' => $token
                ]
            ], 201);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
