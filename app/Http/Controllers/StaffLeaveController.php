<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffLeave\StoreStaffLeaveRequest;
use App\Http\Requests\StaffLeave\UpdateStaffLeaveRequest;
use App\Repositories\StaffLeave\StaffLeaveRepositoryInterface;
use App\Http\Traits\ResolvesIndexFiltersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLeaveController extends AbstractCrudController
{
    use ResolvesIndexFiltersTrait;

    protected string $resourceName = 'Staff Leave';
    protected StaffLeaveRepositoryInterface $leaveRepository;

    public function __construct(StaffLeaveRepositoryInterface $leaveRepository)
    {
        parent::__construct($leaveRepository);
        $this->leaveRepository = $leaveRepository;
    }
    public function staffIndex(Request $request)
    {
        return $this->successResponse(
            $this->leaveRepository->staffLeaves(
                $this->getIndexFilters($request)
            )
        );
    }

    public function store(StoreStaffLeaveRequest $request)
    {
        return $this->successResponse(
            $this->leaveRepository->applyLeave(
                $request->validated()
            ),
            'Leave application submitted successfully.',
            201
        );
    }
    public function update(UpdateStaffLeaveRequest $request, int|string $id)
    {
        return $this->updateResource($id, $request->validated(), $this->repository, $this->resourceName());
    }

    /**
     * Admin sees all leaves.
     */
    public function adminIndex(Request $request)
    {
        return $this->successResponse(
            $this->leaveRepository->staffLeaves(
                $this->getIndexFilters($request)
            )
        );
    }

    /**
     * Admin approves leave.
     */
    public function approve(int|string $id)
    {
        return $this->successResponse(
            $this->leaveRepository->approveLeave(
                (int) $id,
                Auth::user()->staffProfile->id
            ),
            'Leave approved successfully.'
        );
    }

    /**
     * Admin rejects leave.
     */
    public function reject(int|string $id)
    {
        return $this->successResponse(
            $this->leaveRepository->rejectLeave(
                (int) $id,
                Auth::id()
            ),
            'Leave rejected successfully.'
        );
    }

    protected function resourceName(): string
    {
        return 'StaffLeave';
    }
}
