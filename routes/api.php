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
use App\Http\Controllers\Api\V1\UserController;
use App\Jobs\RequestCreateCourse;
use App\Mail\CourseCreated;
use App\Models\Course;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return $request->user();
})->middleware("jwt.auth");


Route::post("/auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);


Route::get('/students', [StudentController::class, 'index']);
Route::post('/students/suspend', [StudentController::class, 'suspend'])->middleware(['jwt.auth', 'admin']);
Route::get('/instructors', [InstructorController::class, 'index']);
Route::post('/instructors/suspend', [InstructorController::class, 'suspend'])->middleware(['jwt.auth', 'admin']);

Route::middleware('jwt.auth')->group(function () {
    Route::delete("/auth/logout", [AuthController::class, "destroy"]);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('courses/{course}/social-link', [SocialLinkController::class, 'show']);
    Route::post('courses/{course}/social-link', [SocialLinkController::class, 'store']);
    Route::patch('courses/{course}/social-link', [SocialLinkController::class, 'update']);
    Route::apiResource('instructors', InstructorController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('users', UserController::class);
    Route::middleware('admin')->group(function() {
        Route::post('instructors/{instructor}/suspend', [InstructorController::class, 'suspend']);
        Route::post('students/{student}/suspend', [StudentController::class, 'suspend']);
    });
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

// courses api
Route::get("/courses", [CourseController::class, "index"]);
Route::get("/courses/{course}", [CourseController::class, "show"]);
Route::post("/courses", [CourseController::class, "store"])->middleware(["jwt.auth"]);
Route::put("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::patch("/courses/publish/{course}", [CourseController::class, "publish"])->middleware(["jwt.auth", "admin"]);
Route::patch("/courses/unpublish/{course}", [CourseController::class, "publish"])->middleware(["jwt.auth", "can:update,course"]);
Route::patch("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::post("/courses/{course}/thumbnail", [CourseController::class, "updateThumbnail"])->middleware(["jwt.auth"]);
Route::delete("/courses/{id}", [CourseController::class, "destroy"])->middleware(["jwt.auth", "can:delete,course"]);
Route::patch("/courses/{course}/complete", [CourseController::class, "complete"])->middleware(["jwt.auth"]);
Route::get("/courses/{course}/request", [CourseController::class, "request"])->middleware("jwt.auth", "can:update,course");

Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::post("/enroll/{course}", [EnrollmentController::class, "enroll"])->middleware('jwt.auth');
Route::post("/unroll/{course}", [EnrollmentController::class, "unroll"])->middleware('jwt.auth');


// lesson api

Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/lessons/{id}', [LessonController::class, 'show'])->middleware('jwt.auth', 'can:view,lesson');;
Route::post('/lessons', [LessonController::class, 'store'])->middleware('jwt.auth', 'can:create,lesson');
Route::put('/lessons/{id}', [LessonController::class, 'update'])->middleware('jwt.auth', 'can:update,lesson');
Route::patch('/lessons/publish/{lesson}', [LessonController::class, 'publish'])->middleware('jwt.auth', 'can:update,lesson');
Route::patch('/lessons/unpublish/{lesson}', [LessonController::class, 'publish'])->middleware('jwt.auth', 'can:update,lesson');
Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->middleware('jwt.auth', 'can:delete,lesson');

Route::get('/courses/{course}/lessons', [LessonController::class, 'index']);
Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->middleware('jwt.auth', 'can:view,lesson');
Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->middleware('jwt.auth', 'can:create,lesson');
Route::put('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->middleware('jwt.auth', 'can:update,lesson');
Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->middleware('jwt.auth', 'can:delete,lesson');

Route::post('/lessons/uploadUrl', [LessonController::class, 'uploadUrl'])->middleware('jwt.auth', 'can:uploadVideoUrl,lesson');

Route::post("/admins/login", [AdminController::class, 'login']);
Route::post("/admins/create", [AdminController::class, 'create']);
Route::get("/admins", [AdminController::class, 'index']);

Route::get('/dashboard/admins', [AdminController::class, 'getAllAdmins']);
Route::get('/dashboard/students', [AdminController::class, 'getAllStudents']);
Route::get('/dashboard/instructors', [AdminController::class, 'getAllInstructors']);

// Route::get('/dashboard/{instructor}/courses', [AdminController::class, 'getCourseFromInstructor']);
Route::get('/dashboard/courses/{course}/students', [AdminController::class, 'getStudentsFromCourse']);
