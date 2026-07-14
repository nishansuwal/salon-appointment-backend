<?php

namespace App\Repositories\Review;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function store(array $data): Review
    {
        return Review::create($data);
    }

    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->refresh();
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Review::with(['user:id,name', 'product:id, name'])
            ->latest()
            ->paginate($perPage);
    }

    public function getByProduct(int $productId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::with('user')
            ->where('product_id', $productId)
            ->latest()
            ->paginate($perPage);
    }

    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Review::with('product')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
