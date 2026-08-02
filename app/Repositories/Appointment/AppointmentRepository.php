<?php

namespace App\Repositories\Appointment;

use App\Models\Appointment;
use App\Models\Service;
use App\Repositories\AbstractCrudRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Traits\HandlesAppointmentCalculation;
use Illuminate\Support\Facades\Auth;

class AppointmentRepository extends AbstractCrudRepository implements AppointmentRepositoryInterface
{
    use HandlesAppointmentCalculation;
    protected string $modelClass = Appointment::class;

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $services = $data['appointment_services'];

            unset($data['appointment_services']);

            $subtotal = 0;

            foreach ($services as &$item) {

                $service = Service::findOrFail($item['service_id']);

                $item['price'] = $service->price;

                $subtotal += $service->price;
            }

            $discount = 0;

            $total = $subtotal - $discount;

            $appointment = Appointment::create([
                'user_id' => Auth::id(),
                'appointment_code' => $this->generateAppointmentCode(),
                'appointment_date' => $data['appointment_date'],
                'start_time' => $data['start_time'],
                'end_time' => $this->calculateEndTime(
                    $services,
                    $data['start_time']
                ),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            $currentTime = Carbon::parse($data['start_time']);
            foreach ($services as $service) {
                $serviceModel = Service::findOrFail($service['service_id']);

                $serviceStart = $currentTime->copy();

                $serviceEnd = $currentTime
                    ->copy()
                    ->addMinutes($serviceModel->duration_minutes);
                $appointment->appointmentServices()->create([
                    'service_id' => $service['service_id'],
                    'staff_id' => $service['staff_id'],
                    'price' => $service['price'],
                    'start_time' => $serviceStart->format('H:i:s'),
                    'end_time' => $serviceEnd->format('H:i:s'),
                    'status' => 'pending',
                    'notes' => $service['notes'] ?? null
                ]);
                $currentTime = $serviceEnd;
            }

            return $appointment->load([
                'appointmentServices.service',
                'appointmentServices.staff.user',
            ]);
        });
    }

    public function updateById(int|string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {

            $appointment = $this->findOrFail($id);

            // Customer can edit only pending appointments
            if (
                Auth::user()->role === 'customer' &&
                $appointment->status !== 'pending'
            ) {
                abort(403, 'Only pending appointments can be updated.');
            }

            // Admin can edit only pending or confirmed appointments
            if (
                Auth::user()->role === 'admin' &&
                ! in_array($appointment->status, ['pending', 'confirmed'])
            ) {
                abort(403, 'This appointment can no longer be updated.');
            }

            $services = $data['appointment_services'];

            unset($data['appointment_services']);

            $subtotal = 0;

            foreach ($services as &$item) {

                $service = Service::findOrFail($item['service_id']);

                $item['price'] = $service->price;

                $subtotal += $service->price;
            }

            $discount = 0;

            $total = $subtotal - $discount;

            $appointment->update([
                'appointment_date' => $data['appointment_date'],
                'start_time' => $data['start_time'],
                'end_time' => $this->calculateEndTime(
                    $services,
                    $data['start_time']
                ),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            // Remove old appointment services
            $appointment->appointmentServices()->delete();

            $currentTime = Carbon::parse($data['start_time']);

            foreach ($services as $service) {

                $serviceModel = Service::findOrFail($service['service_id']);

                $serviceStart = $currentTime->copy();

                $serviceEnd = $serviceStart
                    ->copy()
                    ->addMinutes($serviceModel->duration_minutes);

                $appointment->appointmentServices()->create([
                    'service_id' => $service['service_id'],
                    'staff_id' => $service['staff_id'],
                    'price' => $serviceModel->price,
                    'start_time' => $serviceStart->format('H:i:s'),
                    'end_time' => $serviceEnd->format('H:i:s'),
                    'status' => 'pending',
                    'notes' => $service['notes'] ?? null,
                ]);

                $currentTime = $serviceEnd;
            }

            return $appointment->refresh()->load([
                'appointmentServices.service',
                'appointmentServices.staff.user',
            ]);
        });
    }
    /**
     * Customer cancels only their own appointment.
     */
    public function cancelAppointment(
        int $userId,
        int|string $appointmentId
    ): Model {
        $appointment = $this->query()
            ->whereKey($appointmentId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $appointment->update([
            'status' => 'cancelled',
        ]);

        return $appointment->refresh();
    }

    /**
     * Staff updates only appointments assigned to them.
     */
    public function staffUpdateStatus(
        int|string $appointmentId,
        string $status
    ): Model {
        $staffId = Auth::user()->staffProfile->id;

        $appointment = $this->query()
            ->whereKey($appointmentId)
            ->whereHas('services', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->firstOrFail();

        $appointment->update([
            'status' => $status,
        ]);

        return $appointment->refresh();
    }

    /**
     * Admin updates any appointment.
     */
    public function adminUpdateStatus(
        int|string $appointmentId,
        string $status
    ): Model {
        $appointment = $this->findOrFail($appointmentId);

        $appointment->update([
            'status' => $status,
        ]);

        return $appointment->refresh();
    }

    /**
     * Get appointments assigned to authenticated staff.
     */
    public function staffAppointments(
        array $filters = []
    ): LengthAwarePaginator {
        $staffId = Auth::user()->staffProfile->id;
        if (!$staffId) {
            abort(403, 'Authenticated user is not associated with a staff profile.');
        }
        $query = $this->appointmentQuery()
            ->whereHas('appointmentServices', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            });

        if (!empty($filters['search'])) {
            $this->applyAppointmentSearch($query, $filters);
        }

        return $query
            ->orderBy($filters['column'], $filters['sort'])
            ->paginate(
                $filters['perPage']
            );
    }

    public function userShow(
        int|string $appointmentId
    ): Model {
        $appointment = $this->query()
            ->whereKey($appointmentId)
            ->where('user_id', Auth::id())
            ->with([
                'appointmentServices.service',
                'appointmentServices.staff.user',
            ])
            ->firstOrFail();

        return $appointment;
    }

    public function staffShow(
        int|string $appointmentId
    ): Model {
        $staffId = Auth::user()->staffProfile->id;
        if (!$staffId) {
            abort(403, 'Authenticated user is not associated with a staff profile.');
        }

        $appointment = $this->query()
            ->whereKey($appointmentId)
            ->whereHas('appointmentServices', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->with([
                'appointmentServices.service',
                'appointmentServices.staff.user',
            ])
            ->firstOrFail();

        return $appointment;
    }

    /**
     * Get all appointments for user.
     */
    public function getUserAppointments(
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->appointmentQuery()
            ->where('user_id', Auth::id());

        if (!empty($filters['search'])) {
            $this->applyAppointmentSearch($query, $filters);
        }

        return $query
            ->orderBy($filters['column'], $filters['sort'])
            ->paginate(
                $filters['perPage']
            );
    }

    protected function appointmentQuery()
    {
        return $this->query()->with([
            'user:id,name,email',
            'appointmentServices.service:id,name',
            'appointmentServices.staff.user:id,name',
        ]);
    }

    protected function applyAppointmentSearch(
        $query,
        array $filters
    ): void {
        $search = trim($filters['search'] ?? '');

        if ($search === '') {
            return;
        }

        $query->where(function ($query) use ($search) {
            $query
                ->where('appointment_code', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas(
                    'appointmentServices.service',
                    function ($serviceQuery) use ($search) {
                        $serviceQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
        });
    }
}
