<?php

namespace App\Repositories\StaffProfile;

use App\Models\StaffProfile;
use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use App\Repositories\AbstractCrudRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StaffProfileRepository extends AbstractCrudRepository implements StaffProfileRepositoryInterface
{
    protected string $modelClass = StaffProfile::class;

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $categories = $data['categories'] ?? [];
            unset($data['categories']);

            $staff = StaffProfile::create($data);

            $staff->categories()->sync($categories);

            return $staff->load('categories');
        });
    }

    public function updateById(int|string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {

            $categories = $data['categories'] ?? [];
            unset($data['categories']);

            $staff = $this->findOrFail($id);

            $staff->update($data);

            $staff->categories()->sync($categories);

            return $staff->load('categories');
        });
    }

    public function availableStaff(
        int|string $serviceId,
        string $date,
        string $startTime
    ) {
        $service = Service::findOrFail($serviceId);

        $requestedStart = Carbon::parse(
            "{$date} {$startTime}"
        );

        $requestedEnd = $requestedStart
            ->copy()
            ->addMinutes($service->duration_minutes);

        $dayOfWeek = strtolower(
            $requestedStart->format('D')
        );

        return StaffProfile::query()
            ->active()

            /*
         * Staff must provide this service/category.
         */
            ->whereHas('categories', function ($query) use ($service) {
                $query->where(
                    'categories.id',
                    $service->category_id
                );
            })

            /*
         * Staff must be available during
         * the entire requested service.
         */
            ->whereHas('availability', function ($query) use (
                $dayOfWeek,
                $requestedStart,
                $requestedEnd
            ) {

                $query
                    ->where('day_of_week', $dayOfWeek)
                    ->where(
                        'start_time',
                        '<=',
                        $requestedStart->format('H:i:s')
                    )
                    ->where(
                        'end_time',
                        '>=',
                        $requestedEnd->format('H:i:s')
                    );
            })

            /*
         * Staff must NOT be on approved leave.
         */
            ->whereDoesntHave('leaves', function ($query) use ($date) {

                $query
                    ->where('status', 'approved')
                    ->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
            })

            /*
         * Staff must NOT already have an overlapping
         * appointment.
         */
            ->whereDoesntHave('appointmentServices', function ($query) use (
                $date,
                $requestedStart,
                $requestedEnd
            ) {

                $query
                    ->whereHas('appointment', function ($appointmentQuery) use ($date) {
                        $appointmentQuery
                            ->whereDate('appointment_date', $date);
                    })

                    ->whereNotIn('status', [
                        'cancelled',
                    ])

                    ->where(function ($query) use (
                        $requestedStart,
                        $requestedEnd
                    ) {

                        $query
                            ->where(
                                'start_time',
                                '<',
                                $requestedEnd->format('H:i:s')
                            )
                            ->where(
                                'end_time',
                                '>',
                                $requestedStart->format('H:i:s')
                            );
                    });
            })

            ->with([
                'user:id,name,email',
                'categories:id,name',
            ])

            ->get();
    }
}
