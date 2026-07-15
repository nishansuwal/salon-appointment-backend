<?php

namespace App\Repositories\Service;

use App\Repositories\Service\ServiceRepositoryInterface;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getAll()
    {
        return Service::latest()->paginate(10);
    }

    public function find(int $id)
    {
        return Service::findOrFail($id);
    }

    public function create(array $data)
    {
        return Service::create($data);
    }

    public function update(Service $service, array $data)
    {
        $service->update($data);

        return $service;
    }

    public function delete(Service $service)
    {
        return $service->delete();
    }
}
