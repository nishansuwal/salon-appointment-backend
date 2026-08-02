<?php

namespace App\Repositories\StaffLeave;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface StaffLeaveRepositoryInterface extends CrudRepositoryInterface
{
    public function staffLeaves(
        array $filters = []
    ): LengthAwarePaginator;

    public function applyLeave(
        array $data
    ): Model;

    public function approveLeave(
        int $leaveId,
        int $adminId
    ): Model;

    public function rejectLeave(
        int $leaveId,
        int $adminUserId
    ): Model;
}
