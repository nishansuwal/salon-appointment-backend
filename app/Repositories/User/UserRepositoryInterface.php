<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function getAll(array $filters): LengthAwarePaginator;

    public function find(User $user): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function restore(int $id): User;
}