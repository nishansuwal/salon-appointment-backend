<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Enums\TokenAbility;
use Carbon\Carbon;


class LoginController extends Controller
{
    use ApiResponseTrait;
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.expiration'));
            $refreshTokenExpiration = Carbon::now()->addMinutes(config('sanctum.rt_expiration'));
            // $token = $user->createToken('Password Grant Client')->accessToken;
            $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], $accessTokenExpiration);
            $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], $refreshTokenExpiration);
            // $token = $user->createToken('Password Grant Client')->plainTextToken;
            $data = [
                'userData' => $user,
                'accessToken' =>  $accessToken->plainTextToken,
                'refreshToken' => $refreshToken->plainTextToken,
            ];
            return $this->successResponse($data);
        }
        $message = 'Username or password does not match';
        return $this->errorResponse($message, [], 401);
    }
}
