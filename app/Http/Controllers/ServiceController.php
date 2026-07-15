<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Repositories\Service\ServiceRepositoryInterface;
use App\Models\Service;
use App\Http\Traits\ApiResponseTrait;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ServiceRepositoryInterface $serviceRepository
    ) {}

    public function index()
    {
        return $this->successResponse(
            $this->serviceRepository->getAll()
        );
    }

    public function store(StoreServiceRequest $request)
    {
        $service = $this->serviceRepository->create(
            $request->validated()
        );

        return $this->successResponse(
            $service,
            'Service created successfully.',
            201
        );
    }

    public function show(Service $service)
    {
        return $this->successResponse($service);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service = $this->serviceRepository->update(
            $service,
            $request->validated()
        );

        return $this->successResponse(
            $service,
            'Service updated successfully.'
        );
    }

    public function destroy(Service $service)
    {
        $this->serviceRepository->delete($service);

        return $this->successResponse(
            [],
            'Service deleted successfully.'
        );
    }
}
