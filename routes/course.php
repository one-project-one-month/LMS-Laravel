<?php

use App\Http\Controllers\Api\V1\CourseController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["jwt.auth"]], function () {
       
       Route::post("/courses", [CourseController::class, "store"])->middleware(["instructor"]);
       Route::get("/courses/{course}/request", [CourseController::class, "requestAdmin"])->middleware("can:update,course");
       Route::get("/courses", [CourseController::class, "index"])->withoutMiddleware("jwt.auth");
       Route::get("/courses/{course}", [CourseController::class, "show"])->withoutMiddleware("jwt.auth");
       Route::put("/courses/{course}", [CourseController::class, "update"])->middleware(["can:update,course"]);
       Route::patch("/courses/{course}", [CourseController::class, "update"])->middleware(["can:update,course"]);
       Route::patch("/courses/publish/{course}", [CourseController::class, "publish"])->middleware(["admin"]);
       Route::patch("/courses/unpublish/{course}", [CourseController::class, "unpublish"])->middleware(["can:update,course"]);
       Route::post("/courses/{course}/thumbnail", [CourseController::class, "updateThumbnail"])->middleware(["can:update,course"]);
       Route::delete("/courses/{course}", [CourseController::class, "destroy"])->middleware(["can:delete,course"]);

       //? enrollment api
       Route::patch("/courses/{course}/complete", [CourseController::class, "complete"])->middleware(["can:completeCourse,course"]);
   });