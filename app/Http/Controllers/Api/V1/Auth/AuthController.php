<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Models\Instructor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Requests\StudentLoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\InstructorLoginRequest;
use App\Http\Requests\InstructorRegisterRequest;
use App\Models\RefreshToken;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ResponseTraits;




    public function refreshToken(Request $request)
    {
        $refresh_token = $request->cookie("refreshToken");
        $token = JWTAuth::getToken();


        if (!$token) {
            return response()->json([
                'message' => 'Token not provided'
            ], Response::HTTP_UNAUTHORIZED);
        }



        $refresh_query = RefreshToken::where("refresh_token", $refresh_token)->first();


        if ($refresh_query->isExpired()) {
            return $this->errorResponse(message: "Refresh Token is expired", status: Response::HTTP_FORBIDDEN);
        }


        $user = $refresh_query->user;
        if (!$user) {
            $this->errorResponse(message: "Invalid Refresh_token", status: Response::HTTP_NOT_FOUND);
        }
        $new_refresh_token = generateRefreshToken();

        $user->refreshToken->update(["refresh_token" => $new_refresh_token, "expired_at" => now()->addMinutes(10)]);


        $newToken = JWTAuth::fromUser($user);

        // JWTAuth::invalidate($token); // i think this is no need cause refresh is auto invalid old token
        return $this->successResponseWithToken(message: "Token Refresh Successfully", token: $newToken)->cookie("refreshToken", $new_refresh_token, 60 * 24 * 7, null, null, false, true);
    }



    public function register(RegisterRequest $request)
    {

        if ($request->input("role") === "instructor") {
            [$token, $refresh_token] =   app(InstructorAuthController::class)->register($request);

            // return response()->json(["token" => $token, "refresh_token" => $refresh_token]);
            return $this->successResponseWithToken(message: "Instructor registered successfully", token: $token, status: Response::HTTP_CREATED)->cookie("refreshToken", $refresh_token, 60 * 24 * 7, null, null, false, true);
        } elseif ($request->input("role") === "student") {
            [$token, $refresh_token] =   app(StudentAuthController::class)->register($request);

            return $this->successResponseWithToken(message: "Student registered successfully", token: $token, status: Response::HTTP_CREATED)->cookie("refreshToken", $refresh_token, 60 * 24 * 7, null, null, false, true);
        }
    }
    public function login(StudentLoginRequest $request)
    {

        $credentials = $request->safe()->only(['email', 'password']);
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::where("email", $credentials["email"])->first();
        $refresh_token = generateRefreshToken();
        if (!$user->refreshToken) {
            $user->refreshToken()->create(["refresh_token" => $refresh_token, "expired_at" => now()->addMinutes(10)]);
        }
        $user->refreshToken()->update(["refresh_token" => $refresh_token, "expired_at" => now()->addMinutes(10)]);
        return $this->successResponseWithToken(message: "Login successfully", token: $token, refresh_token: $refresh_token)->cookie("refreshToken", $refresh_token, 60 * 24 * 7, null, null, false, true);
    }
    public function profile()
    {
        $user =   auth()->user();
        $userRole = get_role_name($user->role_id);
        return $this->successResponse(message: "Retrieve profile successfully", data: [...$user->toArray() , "roleName" => $userRole ]);
    }
    public function logout()
    {

        JWTAuth::parseToken()->invalidate();
        auth()->user()->refreshToken()->delete();
        return $this->successResponse(message: "Logout successfully");
    }
}
