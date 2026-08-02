<?php

namespace App\Repositories;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCrudRepository implements CrudRepositoryInterface
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected function query()
    {
        return $this->modelClass::query();
    }

    public function index(array $filters = [], array $options = [])
    {
        $query = $this->query();

        // Eager load
        if (!empty($options['with'])) {
            $query->with($options['with']);
        }

        if (empty($filters)) {
            return $query->get();
        }

        // Search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters, $options) {

                foreach ($options['search'] ?? [] as $field) {

                    if (str_contains($field, '.')) {

                        [$relation, $column] = explode('.', $field, 2);

                        $q->orWhereHas($relation, function ($relationQuery) use ($column, $filters) {
                            $relationQuery->where($column, 'like', "%{$filters['search']}%");
                        });
                    } else {

                        $q->orWhere($field, 'like', "%{$filters['search']}%");
                    }
                }
            });
        }

        return $query
            ->orderBy($filters['column'], $filters['sort'])
            ->paginate($filters['perPage']);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->latest()->paginate($perPage);
    }


    public function findOrFail(int|string $id, array $options = []): Model
    {
        $query = $this->query();

        if (! empty($options['with'])) {
            $query->with($options['with']);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->modelClass::create($data);
    }

    public function updateById(int|string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);

        return $model->refresh();
    }

    public function deleteById(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }
}
