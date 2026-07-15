<?php

namespace App\Repositories\Service;

use App\Models\Service;

interface ServiceRepositoryInterface
{
    public function getAll();

    public function find(int $id);

    public function create(array $data);

    public function update(Service $service, array $data);

    public function delete(Service $service);
}