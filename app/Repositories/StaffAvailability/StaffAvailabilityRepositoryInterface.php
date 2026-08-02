<?php

namespace App\Repositories\StaffAvailability;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface StaffAvailabilityRepositoryInterface
    extends CrudRepositoryInterface
{
    public function staffAvailability(
        int $staffUserId
    ): Collection;

    public function adminAvailability(
        int|string $staffId = null
    ): Collection;

    public function createForStaff(
        int $staffUserId,
        array $data
    ): Model;

    public function updateForStaff(
        int $staffUserId,
        int|string $id,
        array $data
    ): Model;

    public function deleteForStaff(
        int $staffUserId,
        int|string $id
    ): bool;
}