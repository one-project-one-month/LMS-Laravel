<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ResponseTraits
{
    public function errorResponse($message, $error = "", $status = 500)
    {
        return response()->json([
            "message" => $message,
            "error" => $error
        ], $status);
    }
    public function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            "message" => $message,
            "data" => $data,

        ], $status);
    }
    public function successResponseWithToken($message, $token = null, $refresh_token = null, $status = Response::HTTP_OK)
    {
        return response()->json([
            "message" => $message,
            "token" => $token,
            "refreshToken" => $refresh_token

        ], $status);
    }
}
