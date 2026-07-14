<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\TokenAbility;
use Illuminate\Http\Request;
use App\Http\Traits\ApiResponseTrait;
use Laravel\Sanctum\PersonalAccessToken;

class RefreshTokenController extends Controller
{
    use ApiResponseTrait;

    public function refresh(Request $request)
    {
        $refreshToken = PersonalAccessToken::findToken($request->refresh_token);
        if (!$refreshToken || !$this->isValidRefreshToken($refreshToken)) {
            if ($refreshToken) {
                $user = $refreshToken->tokenable;
                $user->tokens()->delete();
            }
            return $this->errorResponse('Invalid refresh token', [], 400);
        }

        // Get the user from the refresh token
        $user = $refreshToken->tokenable;

        // Revoke the old refresh token & access token
        $user->tokens()->delete();

        $accessTokenExpiration = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $refreshTokenExpiration = Carbon::now()->addMinutes(config('sanctum.rt_expiration'));
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value], $accessTokenExpiration);
        $refreshToken = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], $refreshTokenExpiration);

        return $this->successResponse([
            'accessToken' => $accessToken->plainTextToken,
            'refreshToken' => $refreshToken->plainTextToken,
        ]);
    }

    //Returns true if the refresh token provided has token ability to issue access token and is not expired
    private function isValidRefreshToken($token)
    {
        return $token->can(TokenAbility::ISSUE_ACCESS_TOKEN->value) &&
            $token->created_at->addMinutes(config('sanctum.rt_expiration'))->isFuture();
    }
}
