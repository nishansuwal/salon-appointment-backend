<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffAvailability\StoreStaffAvailabilityRequest;
use App\Http\Requests\StaffAvailability\UpdateStaffAvailabilityRequest;
use App\Repositories\StaffAvailability\StaffAvailabilityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;

class StaffAvailabilityController extends Controller
{
    use ApiResponseTrait;
    public function __construct(
        protected StaffAvailabilityRepositoryInterface $availabilityRepository
    ) {
        // parent::__construct($availabilityRepository);
    }

    protected function resourceName(): string
    {
        return 'Staff Availability';
    }

    /**
     * Staff views own availability.
     */
    public function index()
    {
        return $this->successResponse(
            $this->availabilityRepository->staffAvailability(
                Auth::id()
            )
        );
    }

    /**
     * Staff creates availability.
     */
    public function store(
        StoreStaffAvailabilityRequest $request
    ) {
        return $this->successResponse(
            $this->availabilityRepository->createForStaff(
                Auth::id(),
                $request->validated()
            ),
            'Staff Availability created successfully.',
            201
        );
    }

    /**
     * Staff updates own availability.
     */
    public function update(
        UpdateStaffAvailabilityRequest $request,
        int|string $id
    ) {
        return $this->successResponse(
            $this->availabilityRepository->updateForStaff(
                Auth::id(),
                $id,
                $request->validated()
            ),
            'Staff Availability updated successfully.'
        );
    }

    /**
     * Staff deletes own availability.
     */
    public function destroy(int|string $id)
    {
        $this->availabilityRepository->deleteForStaff(
            Auth::id(),
            $id
        );

        return $this->successResponse(
            [],
            'Staff Availability deleted successfully.'
        );
    }

    /**
     * Admin sees all availability.
     */
    public function adminIndex(Request $request)
    {
        return $this->successResponse(
            $this->availabilityRepository->adminAvailability(
                $request->input('staff_id')
            )
        );
    }
}
