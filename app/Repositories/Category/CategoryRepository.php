<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getCategoriesByLevelForClient(string $level)
    {
        if (!in_array($level, ['main', 'child'], true)) {
            throw ValidationException::withMessages([
                'level' => 'Level must be either main or child.',
            ]);
        }

        if ($level === 'main') {
            return Category::with([
                'children' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->withCount('services')
                        ->orderBy('name');
                },
            ])
                ->where('is_active', true)
                ->where('level', 'main')
                ->withCount('services')
                ->orderBy('name')
                ->get()
                ->map(function ($category) {
                    $category->total_services = $category->children->sum(
                        'services_count'
                    );

                    return $category;
                });
        }

        return Category::with('parent:id,name')
            ->where('is_active', true)
            ->where('level', 'child')
            ->withCount('services')
            ->orderBy('name')
            ->get();
    }

    public function store(array $data): Category
    {
        return Category::create($data);
    }

    public function index(array $filters = [], array $options = []): LengthAwarePaginator
    {
        $options = array_replace([
            'with' => ['parent:id,name'],
            'search' => ['name', 'slug'],
            'sortable' => ['id', 'name', 'created_at'],
            'filters' => ['parent_id', 'is_active', 'level'],
        ], $options);
        $sortColumn = in_array($filters['column'] ?? 'id', $options['sortable'], true)
            ? $filters['column'] ?? 'id'
            : 'id';
        $sortDirection = ($filters['sort'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $search = $filters['search'] ?? null;

        return Category::query()
            ->with($options['with'])
            ->when($search, function ($query) use ($search, $options): void {
                $query->where(function ($query) use ($search, $options): void {
                    foreach ($options['search'] as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            })
            ->when(array_key_exists('parent_id', $filters), fn($query) => $query->where('parent_id', $filters['parent_id']))
            ->when(array_key_exists('is_active', $filters), fn($query) => $query->where('is_active', $filters['is_active']))
            ->when(isset($filters['level']), fn($query) => $query->where('level', $filters['level']))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(min(max((int) ($filters['perPage'] ?? 15), 1), 100));
    }

    public function find(Category $category): Category
    {
        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->fresh(['parent:id,name']);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
