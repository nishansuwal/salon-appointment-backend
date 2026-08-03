<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    public function getCategoriesByLevelForClient(string $level);

    public function store(array $data): Category;

    public function index(array $filters = [], array $options = []): LengthAwarePaginator;

    public function find(Category $category): Category;

    public function update(Category $category, array $data): Category;

    public function delete(Category $category): bool;
}
