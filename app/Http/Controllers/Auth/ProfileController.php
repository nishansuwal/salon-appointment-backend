<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Traits\HandlesImagesTrait;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use Throwable;

class ProfileController extends Controller
{
    use ApiResponseTrait;
    use HandlesImagesTrait;

    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function show(Request $request)
    {
        return $this->successResponse($request->user(), 'Profile retrieved successfully.');
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->handleImageUpload(
                    $request,
                    'avatar',
                    'uploads/users',
                    $user->avatar
                );
            }

            return $this->successResponse(
                $this->userRepository->update($user, $data),
                'Profile updated successfully.'
            );
        } catch (Throwable $exception) {
            return $this->handleException($exception, 'updating profile');
        }
    }
}
