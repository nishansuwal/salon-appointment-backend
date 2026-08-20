<?php

namespace App\Repositories\Review;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    public function store(array $data): Review;

    public function update(Review $review, array $data): Review;

    public function delete(Review $review): bool;

    public function getAll(array $filters = []): LengthAwarePaginator;

    public function getByService(int $serviceId);

    public function reviewStatus(int $serviceId, int $userId);

    public function getByUser(int $userId, int $perPage, ?string $status = null): LengthAwarePaginator;

    public function getByStaff(int $staffId, int $perPage): LengthAwarePaginator;
}
