<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function index($search, $perPage, $sort, $sortColumn)
    {
        $category = Category::query();
        if ($search) {
            $category->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');});
        }
        $category->orderBy($sortColumn, $sort);
        return $category->paginate($perPage);
    }

    public function find($category)
    {
        return $category;
    }

    public function update($category, array $data)
    {
        return $category->update($data);
    }

    public function delete($category)
    {
        return $category->delete();
    }
}
