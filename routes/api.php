<?php

use App\Http\Controllers\Api\V1\Auth\AdminController;
use App\Http\Controllers\Api\V1\Auth\InstructorAuthController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\SocialLinkController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\InstructorController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\UpdateProfilePhotoController;
use App\Http\Controllers\Api\V1\UserController;
use App\Jobs\RequestCreateCourse;
use App\Mail\CourseCreated;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Can;
use Tymon\JWTAuth\Facades\JWTAuth;


Route::prefix('v1')->group(function () {
    // authentication
    Route::post("/auth/register", [AuthController::class, "register"]);
    Route::post("/auth/login", [AuthController::class, "login"]);
    Route::delete("/auth/logout", [AuthController::class, "destroy"])->middleware('jwt.auth');

    // User Profile Photo Update
    Route::post('/users/{user}/profile-photo', UpdateProfilePhotoController::class)->middleware('jwt.auth');

    // student
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{student}', [StudentController::class, 'show'])->middleware(['jwt.auth']);
    Route::post('/students', [StudentController::class, 'store'])->middleware(['jwt.auth']);
    Route::put('/students/{student}', [StudentController::class, 'update'])->middleware(['jwt.auth']);
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->middleware(['jwt.auth']);
    Route::post('/students/suspend', [StudentController::class, 'suspend'])->middleware(['jwt.auth', 'admin']);

    // instructor
    Route::get('/instructors', [InstructorController::class, 'index']);
    Route::get('/instructors/{instructor}', [InstructorController::class, 'show'])->middleware(['jwt.auth']);
    Route::post('/instructors', [InstructorController::class, 'store'])->middleware(['jwt.auth']);
    Route::put('/instructors/{instructor}', [InstructorController::class, 'update'])->middleware(['jwt.auth']);
    Route::delete('/instructors/{instructor}', [InstructorController::class, 'destroy'])->middleware(['jwt.auth']);
    Route::post('/instructors/suspend', [InstructorController::class, 'suspend'])->middleware(['jwt.auth', 'admin']);


    // category api
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('jwt.auth');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->middleware('jwt.auth');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->middleware('jwt.auth');
    Route::patch('/categories/{id}', [CategoryController::class, 'update'])->middleware('jwt.auth');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->middleware('jwt.auth');


    // courses api
    Route::get("/courses", [CourseController::class, "index"]);
    Route::post("/courses", [CourseController::class, "store"])->middleware(["jwt.auth"]);
    Route::get("/courses/{course}", [CourseController::class, "show"]);
    Route::put("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
    Route::patch("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
    Route::delete("/courses/{id}", [CourseController::class, "destroy"])->middleware(["jwt.auth", "can:delete,course"]);

    Route::patch("/courses/unpublish/{course}", [CourseController::class, "publish"])->middleware(["jwt.auth", "can:update,course"]);
    Route::patch("/courses/publish/{course}", [CourseController::class, "publish"])->middleware(["jwt.auth", "admin"]);
    Route::post("/courses/{course}/thumbnail", [CourseController::class, "updateThumbnail"])->middleware(["jwt.auth"]);
    Route::patch("/courses/{course}/complete", [CourseController::class, "complete"])->middleware(["jwt.auth"]);
    Route::get("/courses/{course}/request", [CourseController::class, "request"])->middleware("jwt.auth", "can:update,course");


    // social-link api
    Route::get('courses/{course}/social-link', [SocialLinkController::class, 'show'])->middleware('jwt.auth');
    Route::post('courses/{course}/social-link', [SocialLinkController::class, 'store'])->middleware('jwt.auth');
    Route::patch('courses/{course}/social-link', [SocialLinkController::class, 'update'])->middleware('jwt.auth');


    // enrollment
    Route::post("/enroll/{course}", [EnrollmentController::class, "enroll"])->middleware(['jwt.auth', 'isStudent']);


    // lesson api
    Route::get('/courses/{id}/lessons', [LessonController::class, 'index']);
    Route::post('/courses/{id}/lessons', [LessonController::class, 'store'])->middleware('jwt.auth', 'can:create,lesson');
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->middleware('jwt.auth', 'can:view,lesson');
    Route::put('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->middleware('jwt.auth', 'can:update,lesson');
    Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->middleware('jwt.auth', 'can:delete,lesson');
    Route::patch('/courses/{course}/lessons/{lesson}/togglePublish', [LessonController::class, 'publish'])->middleware('jwt.auth', 'can:update,lesson');
    Route::post('/lessons/uploadUrl', [LessonController::class, 'uploadUrl'])->middleware('jwt.auth', 'can:uploadVideoUrl,lesson');

    // admin
    Route::post("/admins/login", [AdminController::class, 'login']);

    // dashboard
    Route::post("/admins/create", [AdminController::class, 'create'])->middleware('jwt.auth', 'admin');
    Route::get('/dashboard/admins', [AdminController::class, 'getAllAdmins'])->middleware('jwt.auth','admin');
    Route::get('/dashboard/instructors', [AdminController::class, 'getAllInstructors'])->middleware('jwt.auth', 'admin');
    Route::get('/dashboard/students', [AdminController::class, 'getAllStudents'])->middleware('jwt.auth', 'admin');
    Route::get('/dashboard/courses', [AdminController::class, 'getCourses'])->middleware('jwt.auth')->middleware('jwt.auth', 'isAdminOrInstructor');
    Route::get('/dashboard/courses/{id}/students', [AdminController::class, 'getStudentsFromCourse'])->middleware('jwt.auth', 'isAdminOrInstructor');
});
