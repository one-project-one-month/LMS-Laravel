<?php

use App\Http\Controllers\Api\V1\Auth\InstructorAuthController;
use App\Http\Controllers\Api\V1\Auth\StudentAuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\SocialLinkController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('student/register', [StudentAuthController::class, 'register']);
Route::post('student/login', [StudentAuthController::class, 'login']);
Route::post('instructor/register', [InstructorAuthController::class, 'register']);
Route::post('instructor/login', [InstructorAuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::delete('student/logout', [StudentAuthController::class, 'logout']);
    Route::delete('instructor/logout', [InstructorAuthController::class, 'logout']);
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
Route::post("/courses", [CourseController::class, "store"])->middleware(["jwt.auth"]);
Route::put("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::patch("/courses/{course}", [CourseController::class, "update"])->middleware(["jwt.auth", "can:update,course"]);
Route::delete("/courses/{id}", [CourseController::class, "destroy"])->middleware(["jwt.auth", "can:delete,course"]);


Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{id}', [CategoryController::class, 'update']);
Route::patch('/categories/{id}', [CategoryController::class, 'update']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


Route::post("/enroll/{course}", [EnrollmentController::class, "enroll"])->middleware('jwt.auth');

// lesson api
Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/lessons/{id}', [LessonController::class, 'show'])->middleware('jwt.auth', 'can:view,lesson');;
Route::post('/lessons', [LessonController::class, 'store'])->middleware('jwt.auth','can:create,lesson');
Route::put('/lessons/{id}', [LessonController::class, 'update'])->middleware('jwt.auth', 'can:update,lesson');
Route::delete('/lessons/{id}', [LessonController::class, 'destroy'])->middleware('jwt.auth', 'can:delete,lesson');
Route::post('/lessons/uploadUrl', [LessonController::class, 'uploadUrl'])->middleware('jwt.auth', 'can:uploadVideoUrl,lesson');

