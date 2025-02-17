<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class InstructorController extends Controller
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
    public function store(Request $request)
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
                'message' => 'Student Registered SuccessFully',
                'data' => [
                    // 'instructor' => array_merge($user->toArray(), $instructor->toArray()),
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
