<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Resources\InstructorCollection;
use App\Http\Resources\InstructorResource;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::all();
        return response()->json([
            'message' => 'Admins lists are as follows.',
            'data' => [
                'admins' => $admins,
            ]
        ]);
    }
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }




            return response()->json([
                'message' => 'Login successfully as Admin ',
                'data' => [

                    'token' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function create(Request $request)
    {
        //need to wrap DB:transistion()


        try {
            $admin_role_id = Role::query()->where("role", "admin")->first()->id;

            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'password' => ['required', 'string', 'min:8'],
            ]);

            $user = User::query()->create(Arr::add($credentials, "role_id", $admin_role_id));
            $user->admin()->create(["user_id" => $user->id]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Admin account create successfully',
                'data' => [
                    'admin' => $user,
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
    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        try {
            //code...
            $admin->update($request->validated());

            return response()->json([
                'message' => 'Admin updated successfully',
                'data' => [
                    'admin' => $admin
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllInstructors()
    {
        $instructors = Instructor::latest()->with('user')->paginate(20);

        if (!$instructors) {
            return response()->json([
                'message' => "Instructors not found."
            ], 404);
        }

        return response()->json([
            'message' => 'Instructors retrieved successfully.',
            'instructors' => new InstructorCollection($instructors)
        ], 200);
    }

    public function getAllAdmins()
    {
        $admins = Admin::latest()->with('user')->paginate(20);

        if (!$admins) {
            return response()->json([
                'message' => "Admins not found."
            ], 404);
        }
        return response()->json([
            'message' => 'Instructors retrieved successfully.',
            'admins' => $admins
        ], 200);
    }
    public function getAllStudents()
    {
        $students = Student::latest()->with('user')->paginate(20);

        if (!$students) {
            return response()->json([
                'message' => "Students not found."
            ], 404);
        }
        return response()->json([
            'message' => 'Instructors retrieved successfully.',
            'students' => $students
        ], 200);
    }
}

