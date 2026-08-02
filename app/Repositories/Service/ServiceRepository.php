<?php

namespace App\Repositories\Service;

use App\Repositories\Service\ServiceRepositoryInterface;
use App\Models\Service;

use App\Repositories\AbstractCrudRepository;

class ServiceRepository extends AbstractCrudRepository implements ServiceRepositoryInterface
{
    protected string $modelClass = Service::class;
}
