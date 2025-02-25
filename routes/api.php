<?php

use App\Http\Controllers\Api\V1\Auth\AdminController;
use App\Http\Controllers\Api\V1\Auth\InstructorAuthController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\StudentAuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/auth/register", RegisterController::class);
Route::post('student/login', [StudentAuthController::class, 'login']);
Route::post('instructor/login', [InstructorAuthController::class, 'login']);

Route::get('students', [StudentController::class, 'index']);
Route::middleware('jwt.auth')->group(function () {
    Route::delete('student/logout', [StudentAuthController::class, 'logout']);
    Route::delete('instructor/logout', [InstructorAuthController::class, 'logout']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
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
Route::get('/lessons/{id}', [LessonController::class, 'show']);
Route::post('/lessons', [LessonController::class, 'store']);
Route::put('/lessons/{id}', [LessonController::class, 'update']);
Route::delete('/lessons/{id}', [LessonController::class, 'destroy']);
Route::post('/lessons/uploadUrl', [LessonController::class, 'uploadUrl']);

Route::post("/admins/login", [AdminController::class, 'login']);
Route::post("/admins/create", [AdminController::class, 'create']);
Route::get("/admins", [AdminController::class, 'index']);
