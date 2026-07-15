<?php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesImagesTrait;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use App\Http\Traits\ApiResponseTrait;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait, HandlesImagesTrait, ResolvesIndexFiltersTrait;

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = $this->getIndexFilters($request);

        return $this->successResponse(
            $this->userRepository->getAll($filters)
        );
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return $this->successResponse(
            $this->userRepository->find($user)
        );
    }

    /**
     * Logged-in user profile.
     */
    public function profile(Request $request)
    {
        return $this->successResponse($request->user());
    }

    /**
     * Update logged-in user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->handleImageUpload(
                $request,
                'avatar',
                'uploads/users'
            );
        }

        return $this->successResponse(
            $this->userRepository->update($user, $data),
            'Profile updated successfully.'
        );
    }

    /**
     * Admin updates a user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'role' => 'sometimes|in:admin,staff,customer',
            'avatar' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->handleImageUpload(
                $request,
                'avatar',
                'uploads/users'
            );
        }

        return $this->successResponse(
            $this->userRepository->update($user, $data),
            'User updated successfully.'
        );
    }

    /**
     * Delete user.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userRepository->delete($user);

        return $this->successResponse(
            "",
            'User deleted successfully.'
        );
    }

    /**
     * Restore soft deleted user.
     */
    public function restore(int $id)
    {
        $user = $this->userRepository->restore($id);

        $this->authorize('restore', $user);

        return $this->successResponse(
            $user,
            'User restored successfully.'
        );
    }
}
