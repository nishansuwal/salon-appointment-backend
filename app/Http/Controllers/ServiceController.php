<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Repositories\Service\ServiceRepositoryInterface;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends AbstractCrudController
{
    use ResolvesIndexFiltersTrait;
    protected string $resourceName = 'Service';

    public function __construct(
        ServiceRepositoryInterface $repository
    ) {
        parent::__construct($repository);
    }
    protected function resourceName(): string
    {
        return $this->resourceName;
    }

    public function index(Request $request)
    {
        return $this->indexResource(
            request: $request,
            repository: $this->repository,
            options: [
                'with' => [
                    'category:id,name,level',
                    'category.staff:id,user_id,position,is_active',
                ],
                'search' => [
                    'name',
                    'price',
                    'category.name',
                ],
            ],
            useIndexFilters: true
        );
    }

    public function show(int|string $id)
    {
        return $this->showResource(
            id: $id,
            repository: $this->repository,
            options: [
                'with' => [
                    'category:id,name,level',
                    'category.staff:id,user_id,position,is_active',
                    'category.staff.user:id,name,email',
                ],
            ]
        );
    }

    public function store(StoreServiceRequest $request)
    {

        return $this->storeResource(
            data: $request->validated(),
            repository: $this->repository,
            name: $this->resourceName
        );
    }

    public function update(
        UpdateServiceRequest $request,
        int|string $id
    ) {
        return $this->updateResource(
            id: $id,
            data: $request->validated(),
            repository: $this->repository,
            name: $this->resourceName
        );
    }

    public function destroy(int|string $id)
    {
        return $this->destroyResource(
            id: $id,
            repository: $this->repository,
            name: $this->resourceName
        );
    }
}
