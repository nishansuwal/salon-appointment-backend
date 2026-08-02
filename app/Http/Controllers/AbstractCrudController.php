<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesCrudResponses;
use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Http\Request;

abstract class AbstractCrudController extends Controller
{
    use HandlesCrudResponses;

    public function __construct(
        protected CrudRepositoryInterface $repository
    ) {}

    public function index(Request $request)
    {
        return $this->indexResource(
            request: $request,
            repository: $this->repository,
            options: $this->indexOptions(),
            useIndexFilters: $this->useIndexFilters()
        );
    }

    public function show(int|string $id)
    {
        return $this->showResource(
            id: $id,
            repository: $this->repository,
            options: $this->showOptions()
        );
    }

    public function destroy(int|string $id)
    {
        return $this->destroyResource(
            id: $id,
            repository: $this->repository,
            name: $this->resourceName()
        );
    }

    /**
     * Options for index queries.
     */
    protected function indexOptions(): array
    {
        return [];
    }

    /**
     * Relationships for detailed show response.
     */
    protected function showOptions(): array
    {
        return [];
    }

    protected function useIndexFilters(): bool
    {
        return true;
    }

    abstract protected function resourceName(): string;
}
