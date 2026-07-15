<?php

namespace App\Repositories\User;

use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function getAll(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->when(
                $filters['search'],
                function ($query) use ($filters) {
                    $query->where(function ($q) use ($filters) {
                        $q->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('email', 'like', "%{$filters['search']}%")
                            ->orWhere('phone', 'like', "%{$filters['search']}%");
                    });
                }
            )
            ->orderBy($filters['column'], $filters['sort'])
            ->paginate($filters['perPage']);
    }

    public function find(User $user): User
    {
        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User|int $user): bool
    {
        $user = $user instanceof User ? $user : User::findOrFail($user);

        return (bool) $user->delete();
    }

    public function restore(int $id): User
    {
        $user = User::withTrashed()->findOrFail($id);

        $user->restore();

        return $user->fresh();
    }
}