<?php

namespace App\Repositories\Address;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AddressRepositoryInterface
{
    public function index(
        ?int $userId,
        string $search,
        int $perPage,
        string $sort,
        string $sortColumn
    ): LengthAwarePaginator;

    public function store(array $data): Address;

    public function update(Address $address, array $data): bool;

    public function delete(Address $address): bool;

    public function find(Address $address): Address;
}
