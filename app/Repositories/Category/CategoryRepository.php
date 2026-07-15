<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategoriesByLevelForClient($level)
    {
        return Category::with('parent:id,name')
            ->where('is_active', true)
            ->where('level', $level)
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function index(array $filters): LengthAwarePaginator
    {
        return Category::query()
            ->when(
                $filters['search'],
                function ($query) use ($filters) {
                    $query->where(function ($q) use ($filters) {
                        $q->where('name', 'like', "%{$filters['search']}%")
                            ->orWhere('description', 'like', "%{$filters['search']}%");
                    });
                }
            )
            ->orderBy($filters['column'], $filters['sort'])
            ->paginate($filters['perPage']);
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
