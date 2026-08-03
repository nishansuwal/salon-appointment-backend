<?php

namespace App\Repositories\User;

use App\Enums\TokenAbility;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => bcrypt($data['password']),
                'role' => 'user',
            ]);

            return [
                'user' => $user,
                'tokens' => $this->createTokens($user),
            ];
        });
    }

    public function login(string $email, string $password): ?array
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return DB::transaction(fn(): array => [
            'user' => $user,
            'tokens' => $this->createTokens($user),
        ]);
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

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

    /**
     * @return array<string, int|string>
     */
    private function createTokens(User $user): array
    {
        $accessExpiration = CarbonImmutable::now()->addMinutes(
            (int) config('sanctum.expiration', 60)
        );
        $refreshExpiration = CarbonImmutable::now()->addMinutes(
            (int) config('sanctum.rt_expiration', 1440)
        );

        $accessToken = $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value],
            $accessExpiration
        );
        $refreshToken = $user->createToken(
            'refresh_token',
            [TokenAbility::ISSUE_ACCESS_TOKEN->value],
            $refreshExpiration
        );

        return [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => (int) config('sanctum.expiration', 60) * 60,
        ];
    }
}
