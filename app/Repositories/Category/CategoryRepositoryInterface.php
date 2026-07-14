<?php

namespace App\Repositories\Category;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function store(array $data): Category;

    public function index($search, $perPage, $sort, $sortColumn);

    public function find($category);

    public function update($category, array $data);

    public function delete($category);
}
