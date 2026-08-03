<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Register a customer and issue their Sanctum credentials atomically.
     *
     * @return array{user: User, tokens: array<string, int|string>}
     */
    public function register(array $data): array;

    /**
     * Verify credentials and issue a new pair of Sanctum credentials.
     *
     * @return array{user: User, tokens: array<string, int|string>}|null
     */
    public function login(string $email, string $password): ?array;

    public function revokeCurrentToken(User $user): void;

    public function getAll(array $filters): LengthAwarePaginator;

    public function find(User $user): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;

    public function restore(int $id): User;
}
