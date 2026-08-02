<?php

namespace App\Http\Controllers;

use App\Http\Requests\Faq\StoreFaqRequest;
use App\Http\Requests\Faq\UpdateFaqRequest;
use App\Repositories\Faq\FaqRepositoryInterface;

class FaqController extends AbstractCrudController
{
    public function __construct(FaqRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function useIndexFilters(): bool
    {
        return false;
    }

    public function store(StoreFaqRequest $request)
    {
        return $this->storeResource($request->validated(), $this->repository, $this->resourceName());
    }
    public function update(UpdateFaqRequest $request, int|string $id)
    {
        return $this->updateResource($id, $request->validated(), $this->repository, $this->resourceName());
    }
    protected function resourceName(): string
    {
        return 'Faq';
    }
}
