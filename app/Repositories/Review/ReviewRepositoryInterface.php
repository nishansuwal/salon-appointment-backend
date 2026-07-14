<?php

namespace App\Repositories\Review;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function store(array $data): Review;

    public function update(Review $review, array $data): Review;

    public function delete(Review $review): bool;

    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function getByProduct(int $productId, int $perPage = 15): LengthAwarePaginator;

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator;
}
