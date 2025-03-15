<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\customPaginationFormat;
use App\Http\Requests\AdminUpdateRequest;
use App\Http\Resources\InstructorResource;

use App\Http\Resources\InstructorCollection;
use App\Http\Resources\CourseStudentsResource;
use App\Interfaces\AdminDashboardInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    use customPaginationFormat;

    public function __construct(protected AdminDashboardInterface $adminDashboard) {}

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


    // get all admins
    public function getAllAdmins(Request $request)
    {
        $admins = $this->adminDashboard->getAllAdmins($request);

        if ($admins->isEmpty()) {
            return errorResponse("Admins not found.");
        }

        return successResponse("Admins retrieved successfully.", $this->paginateFormat($admins));
    }

    // get all students
    public function getAllStudents(Request $request)
    {
        $students = $this->adminDashboard->getAllStudents($request);

        if ($students->isEmpty()) {
            return errorResponse("Students not found.");
        }

        return successResponse('Students retrieved successfully.', $this->paginateFormat($students));
    }


    // get all instructors
    public function getAllInstructors(Request $request)
    {
        $instructors = $this->adminDashboard->getAllInstructors($request);

        if ($instructors->isEmpty()) {
            return errorResponse("Instructors not found.");
        }

        $instructors = $this->paginateFormat(InstructorResource::collection($instructors));

        return successResponse('Instructors retrieved successfully.', $instructors);
    }


    // get courses
    public function getCourses(Request $request)
    {
        $courses = $this->adminDashboard->getCourses($request);

        if ($courses->isEmpty()) {
            return errorResponse("There is no student.");
        }

        return successResponse("Courses retrieved successfully.", $this->paginateFormat($courses));
    }


    // get enrolled students from course
    public function getStudentsFromCourse($id, Request $request)
    {
        $students = $this->adminDashboard->getStudentsFromCourse($id, $request);

        if ($students->isEmpty()) {
            return errorResponse("There is no student.");
        }

        $students = $this->paginateFormat(CourseStudentsResource::collection($students));

        return successResponse("Students retrieved successfully.", $students);
    }


    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json([
                'message' => 'Token not provided'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $isExpired = JWTAuth::parseToken()->check();

        if (!$isExpired) {
            $newToken = JWTAuth::refresh($token);
            JWTAuth::invalidate($token);

            return response()->json([
                'message' => 'Token refreshed successfully.',
                'data' => [
                    'token' => $newToken
                ]
            ]);
        }

        return response()->json([
            'message' => 'Token is still valid.',
            'data' => [
                'token' => (string) $token
            ]
        ]);
    }
}
