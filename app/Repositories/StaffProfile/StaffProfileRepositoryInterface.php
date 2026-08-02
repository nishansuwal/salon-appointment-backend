<?php

namespace App\Repositories\StaffProfile;

use App\Repositories\Contracts\CrudRepositoryInterface;

interface StaffProfileRepositoryInterface extends CrudRepositoryInterface
{
    public function availableStaff(
        int|string $serviceId,
        string $date,
        string $startTime
    );
}
