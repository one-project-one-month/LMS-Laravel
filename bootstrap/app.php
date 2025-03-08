<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\instructorMiddleware;
use App\Http\Middleware\JwtAuthMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth' => JwtAuthMiddleware::class,
            "admin" => AdminMiddleware::class,
            "instructor" => instructorMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (UnauthorizedException $e) {
            return response()->json([
                "message" => "Unauthorized",
                "error" => $e->getMessage()
            ], 403);
        });
        $exceptions->render(function (AccessDeniedHttpException $e) {

            return response()->json([
                "message" => "You are unauthorized to do this action",
                "error" => $e->getMessage()
            ], 403);
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is("api/courses/*")) {

                return response()->json([
                    "message" => "Course is Not found",
                    "error" => $e->getMessage()
                ], 404);
            }
            if ($request->is("api/students/*")) {
                return response()->json([
                    "message" => "Student is Not found",
                    "error" => $e->getMessage()
                ], 404);
            }
            if ($request->is("api/instructors/*")) {
                return response()->json([
                    "message" => "Instructor is Not found",
                    "error" => $e->getMessage()
                ], 404);
            }
            if ($request->is("api/lessons/*")) {
                return response()->json([
                    "message" => "Lesson is Not found",
                    "error" => $e->getMessage()
                ], 404);
            }

            return response()->json([
                "message" => "Resource not found",
                "error" => $e->getMessage()
            ], 404);
        });


        // $exceptions->render(function (Exception $e) {
        //     return response()->json([
        //         "message" => "something went wrong",
        //         "error" => $e->getMessage()
        //     ], 500);
        // });
    })->create();
