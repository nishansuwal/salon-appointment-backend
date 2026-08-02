<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends AbstractCrudController
{
    use ResolvesIndexFiltersTrait;
    protected string $resourceName = 'Appointment';

    public function __construct(
        protected AppointmentRepositoryInterface $appointmentRepository
    ) {
        parent::__construct($appointmentRepository);
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
                    'user:id,name,email',
                    'appointmentServices.service:id,name',
                    'appointmentServices.staff.user:id,name',
                ],
                'search' => [
                    'appointment_code',
                    'status',
                    'user.name',
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
                    'user:id,name,email',
                    'appointmentServices.service:id,name',
                    'appointmentServices.staff.user:id,name',
                ],
            ]
        );
    }

    public function userShow(int|string $id)
    {
         return $this->successResponse(
            $this->appointmentRepository->userShow(
                appointmentId: $id
            ),
            'Appointment details retrieved successfully.'
        );
    }

    public function staffShow(int|string $id)
    {
         return $this->successResponse(
            $this->appointmentRepository->staffShow(
                appointmentId: $id
            ),
            'Appointment details retrieved successfully.'
        );
    }

    public function store(StoreAppointmentRequest $request)
    {

        return $this->storeResource(
            data: $request->validated(),
            repository: $this->repository,
            name: $this->resourceName
        );
    }

    public function update(
        UpdateAppointmentRequest $request,
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

    /**
     * Customer cancels their own appointment.
     */
    public function cancel(int|string $id)
    {
        return $this->successResponse(
            $this->appointmentRepository->cancelAppointment(
                Auth::id(),
                appointmentId: $id
            ),
            'Appointment cancelled successfully.'
        );
    }

    /**
     * Get appointments assigned to the authenticated staff.
     */
    public function staffAppointments(Request $request)
    {
        return $this->successResponse(
            $this->appointmentRepository->staffAppointments(
                $this->getIndexFilters($request)
            )
        );
    }

    /**
     * Staff updates an appointment status.
     */
    public function staffUpdateStatus(
        Request $request,
        int|string $id
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:confirmed,completed,cancelled',
            ],
        ]);

        return $this->successResponse(
            $this->appointmentRepository->staffUpdateStatus(
                appointmentId: $id,
                status: $validated['status']
            ),
            'Appointment status updated successfully.'
        );
    }

    /**
     * Get all appointments for admin.
     */
    public function getUserAppointments(Request $request)
    {
        return $this->successResponse(
            $this->appointmentRepository->getUserAppointments(
                $this->getIndexFilters($request)
            )
        );
    }

    /**
     * Admin updates any appointment status.
     */
    public function adminUpdateStatus(
        Request $request,
        int|string $id
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                'in:confirmed,completed,cancelled',
            ],
        ]);

        return $this->successResponse(
            $this->appointmentRepository->adminUpdateStatus(
                appointmentId: $id,
                status: $validated['status']
            ),
            'Appointment status updated successfully.'
        );
    }
}
