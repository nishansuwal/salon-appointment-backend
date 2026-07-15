<?php

namespace App\Repositories\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function getCategoriesByLevelForClient($level);

    public function store(array $data): Category;

    public function index(array $filters);

    public function find($category);

    public function update($category, array $data);

    public function delete($category);
}
