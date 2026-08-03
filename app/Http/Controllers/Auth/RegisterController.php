<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\User\UserRepositoryInterface;
use Throwable;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function register(RegisterRequest $request)
    {
        try {
            return $this->successResponse(
                $this->userRepository->register($request->validated()),
                'User registered successfully.',
                201
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'registering user');
        }
    }
}
