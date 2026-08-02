<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface CrudRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function index(array $filters = [], array $options = []);
    public function findOrFail(int|string $id, array $options = []): Model;
    public function create(array $data): Model;
    public function updateById(int|string $id, array $data): Model;
    public function deleteById(int|string $id): bool;
}
