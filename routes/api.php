<?php

use App\Http\Controllers\Api\V1\Auth\AdminController;
use App\Http\Controllers\Api\V1\Auth\InstructorAuthController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\SocialLinkController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/auth/register", [AuthController::class, "register"]);
Route::post("/auth/login", [AuthController::class, "login"]);


Route::get('students', [StudentController::class, 'index']);
Route::middleware('jwt.auth')->group(function () {
    Route::delete("/auth/logout", [AuthController::class, "destroy"]);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('courses/{course}/social-link', [SocialLinkController::class, 'show']);
    Route::post('courses/{course}/social-link', [SocialLinkController::class, 'store']);
    Route::patch('courses/{course}/social-link', [SocialLinkController::class, 'update']);

});

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

// courses api
Route::get("/courses", [CourseController::class, "index"]);
Route::get("/courses/{course}", [CourseController::class, "show"]);
Route::post("/courses", [CourseController::class, "store"])->middleware(["jwt.auth"]);
Route::put("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::patch("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::delete("/courses/{id}", [CourseController::class, "destroy"])->middleware(["jwt.auth", "can:delete,course"]);


Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::post("/enroll/{course}", [EnrollmentController::class, "enroll"])->middleware('jwt.auth');
Route::post("/unroll/{course}", [EnrollmentController::class, "unroll"])->middleware('jwt.auth');


// lesson api
Route::get('/courses/{course}/lessons', [LessonController::class, 'index']);
Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->middleware('jwt.auth', 'can:view,lesson');
Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->middleware('jwt.auth', 'can:create,lesson');
Route::put('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->middleware('jwt.auth', 'can:update,lesson');
Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->middleware('jwt.auth', 'can:delete,lesson');
Route::post('/lessons/uploadUrl', [LessonController::class, 'uploadUrl'])->middleware('jwt.auth', 'can:uploadVideoUrl,lesson');

Route::post("/admins/login", [AdminController::class, 'login']);
Route::post("/admins/create", [AdminController::class, 'create']);
Route::get("/admins", [AdminController::class, 'index']);
