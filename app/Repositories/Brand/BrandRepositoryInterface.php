<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    public function index(
        string $search,
        int $perPage,
        string $sort,
        string $sortColumn
    ): LengthAwarePaginator;

    public function store(array $data): Brand;

    public function update(Brand $brand, array $data): bool;

    public function delete(Brand $brand): bool;

    public function find(Brand $brand): Brand;
}
