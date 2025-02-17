<?php

use App\Http\Controllers\Api\Auth\InstructorController;
use App\Http\Controllers\Api\Auth\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/student/register', [StudentController::class, 'store']);
Route::post('/instructor/register', [InstructorController::class, 'store']);
