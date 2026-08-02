<?php

namespace App\Repositories\StaffAvailability;

use App\Models\StaffAvailability;
use App\Repositories\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StaffAvailabilityRepository extends AbstractCrudRepository
implements StaffAvailabilityRepositoryInterface
{
    protected string $modelClass = StaffAvailability::class;

    /**
     * Staff sees own availability.
     */
    public function staffAvailability(
        int $staffUserId
    ): Collection {

        $staffId = Auth::user()
            ->staffProfile
            ->id;

        return StaffAvailability::query()
            ->where('staff_id', $staffId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Admin sees all availability.
     *
     * Optionally filter by staff_id.
     */
    public function adminAvailability(
        int|string $staffId = null
    ): Collection {

        return StaffAvailability::query()
            ->with('staff.user:id,name,email')
            ->when(
                $staffId,
                fn($query) =>
                $query->where('staff_id', $staffId)
            )
            ->orderBy('staff_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Staff creates own availability.
     */
    public function createForStaff(
        int $staffUserId,
        array $data
    ): Model {

        $staffId = Auth::user()
            ->staffProfile
            ->id;

        $this->validateNoOverlap(
            $staffId,
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time']
        );

        return StaffAvailability::create([
            'staff_id' => $staffId,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ]);
    }

    /**
     * Staff updates own availability.
     */
    public function updateForStaff(
        int $staffUserId,
        int|string $id,
        array $data
    ): Model {

        $staffId = Auth::user()
            ->staffProfile
            ->id;

        $availability = StaffAvailability::query()
            ->where('id', $id)
            ->where('staff_id', $staffId)
            ->firstOrFail();

        $this->validateNoOverlap(
            $staffId,
            $data['day_of_week'],
            $data['start_time'],
            $data['end_time'],
            (int) $id
        );

        $availability->update($data);

        return $availability->refresh();
    }

    /**
     * Staff deletes own availability.
     */
    public function deleteForStaff(
        int $staffUserId,
        int|string $id
    ): bool {

        $staffId = Auth::user()
            ->staffProfile
            ->id;

        $availability = StaffAvailability::query()
            ->where('id', $id)
            ->where('staff_id', $staffId)
            ->firstOrFail();

        return (bool) $availability->delete();
    }

    /**
     * Prevent overlapping availability slots.
     */
    protected function validateNoOverlap(
        int $staffId,
        string $day,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): void {

        $exists = StaffAvailability::query()
            ->where('staff_id', $staffId)
            ->where('day_of_week', $day)
            ->when(
                $ignoreId,
                fn($query) =>
                $query->where('id', '!=', $ignoreId)
            )
            ->where(function ($query) use ($startTime, $endTime) {

                $query
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_time' => 'This availability overlaps with an existing availability period.',
            ]);
        }
    }
}
