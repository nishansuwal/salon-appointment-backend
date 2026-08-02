<?php

namespace App\Http\Controllers\Concerns;

use App\Http\Traits\ApiResponseTrait;
use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Traits\ResolvesIndexFiltersTrait;

trait HandlesCrudResponses
{
    use ApiResponseTrait, ResolvesIndexFiltersTrait;

    protected function indexResource(
        Request $request,
        CrudRepositoryInterface $repository,
        array $options = [],
        bool $useIndexFilters
    ) {
        return $this->successResponse(
            $repository->index(
                filters: $useIndexFilters ? $this->getIndexFilters($request) : [],
                options: $options
            )
        );
    }

    protected function showResource(
        int|string $id,
        CrudRepositoryInterface $repository,
        array $options = []
    ) {
        $result = $repository->findOrFail(
            id: $id,
            options: $options
        );

        return $this->successResponse($result);
    }

    protected function storeResource(array $data, CrudRepositoryInterface $repository, string $name)
    {
        return $this->successResponse($repository->create($data), "{$name} created successfully.", 201);
    }

    protected function updateResource(int|string $id, array $data, CrudRepositoryInterface $repository, string $name)
    {
        return $this->successResponse($repository->updateById($id, $data), "{$name} updated successfully.");
    }

    protected function destroyResource(int|string $id, CrudRepositoryInterface $repository, string $name)
    {
        $repository->deleteById($id);

        return $this->successResponse([], "{$name} deleted successfully.");
    }
}
