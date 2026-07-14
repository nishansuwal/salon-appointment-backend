<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrandRepository implements BrandRepositoryInterface
{
    public function index(
        string $search,
        int $perPage,
        string $sort,
        string $sortColumn
    ): LengthAwarePaginator {
        return Brand::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sortColumn, $sort)
            ->paginate($perPage);
    }

    public function store(array $data): Brand
    {
        return Brand::create($data);
    }

    public function update(Brand $brand, array $data): bool
    {
        return $brand->update($data);
    }

    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    public function find(Brand $brand): Brand
    {
        return $brand->fresh();
    }
}
