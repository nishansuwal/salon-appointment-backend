<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Throwable;

class LogoutController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        try {
            $this->userRepository->revokeCurrentToken($request->user());

            return $this->successResponse([], 'Logged out successfully.');
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'logging out');
        }
    }
}
