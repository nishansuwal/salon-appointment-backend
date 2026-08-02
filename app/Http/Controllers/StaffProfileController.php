<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffProfile\StoreStaffProfileRequest;
use App\Http\Requests\StaffProfile\UpdateStaffProfileRequest;
use App\Repositories\StaffProfile\StaffProfileRepositoryInterface;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffProfileController extends AbstractCrudController
{
    use ResolvesIndexFiltersTrait;
    protected string $resourceName = 'Appointment';

    public function __construct(
        protected StaffProfileRepositoryInterface $staffProfileRepository
    ) {
        parent::__construct($staffProfileRepository);
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
                    'user:id,name,email,phone',
                    'categories:id,name,slug',
                ],
                'search' => [
                    'experience',
                    'position',
                    'avg_rating',
                    'user.name',
                    'categories.name',
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
                    'user:id,name,email,phone',
                    'categories:id,name,slug',
                    'availability',
                    'leaves',
                ],
            ]
        );
    }

    public function store(StoreStaffProfileRequest $request)
    {

        return $this->storeResource(
            data: $request->validated(),
            repository: $this->repository,
            name: $this->resourceName
        );
    }

    public function update(
        UpdateStaffProfileRequest $request,
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

    public function availableStaff(Request $request)
    {
        $validated = $request->validate([
            'service_id' => [
                'required',
                'integer',
                'exists:services,id',
            ],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],
        ]);

        return $this->successResponse(
            $this->staffProfileRepository->availableStaff(
                serviceId: $validated['service_id'],
                date: $validated['date'],
                startTime: $validated['start_time']
            )
        );
    }
}
