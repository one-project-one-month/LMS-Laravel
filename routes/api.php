<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\V1\InstructorLoginController;
use App\Http\Controllers\Api\V1\InstructorLogoutController;
use App\Http\Controllers\Api\V1\InstructorRegisterController;
use App\Http\Controllers\Api\V1\StudentLoginController;
use App\Http\Controllers\Api\V1\StudentLogoutController;
use App\Http\Controllers\Api\V1\StudentRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/student/register', StudentRegisterController::class);
Route::post('/student/login', StudentLoginController::class);
Route::post('/instructor/register', InstructorRegisterController::class);
Route::post('/instructor/login', InstructorLoginController::class);

Route::middleware('jwt.auth')->group(function () {
    Route::delete('/student/logout', StudentLogoutController::class);
    Route::delete('/instructor/logout', InstructorLogoutController::class);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

