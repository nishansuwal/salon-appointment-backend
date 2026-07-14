<?php

namespace App\Repositories\Address;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressRepository implements AddressRepositoryInterface
{
    public function index(
        ?int $userId,
        string $search,
        int $perPage,
        string $sort,
        string $sortColumn
    ): LengthAwarePaginator {

        // Protect sortable columns
        $allowedColumns = ['id', 'city', 'state', 'created_at'];
        $sortColumn = in_array($sortColumn, $allowedColumns)
            ? $sortColumn
            : 'id';

        return Address::query()
            ->when($userId, fn ($q) =>
                $q->where('user_id', $userId)
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('city', 'like', "%{$search}%")
                      ->orWhere('state', 'like', "%{$search}%")
                      ->orWhere('street', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortColumn, $sort)
            ->paginate($perPage);
    }

    public function store(array $data): Address
    {
        return Address::create($data);
    }

    public function update(Address $address, array $data): bool
    {
        return $address->update($data);
    }

    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    public function find(Address $address): Address
    {
        return $address;
    }
}
