<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponseTrait;

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            [],
            'Logged out successfully.'
        );
    }
}
