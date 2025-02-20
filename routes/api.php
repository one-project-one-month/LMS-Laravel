<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\V1\InstructorAuthController;
use App\Http\Controllers\Api\V1\StudentAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('student/register', [StudentAuthController::class, 'register']);
Route::post('student/login', [StudentAuthController::class, 'login']);
Route::post('instructor/register', [InstructorAuthController::class, 'register']);
Route::post('instructor/login', [InstructorAuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::delete('student/logout', [StudentAuthController::class, 'logout']);
    Route::delete('instructor/logout', [InstructorAuthController::class, 'logout']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});
