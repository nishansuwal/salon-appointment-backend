<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Models\AppointmentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function store(array $data): Review
    {
        return Review::create($data);
    }

    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->refresh();
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Review::with(['user:id,name', 'service:id, name']);

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value);
            }
        }

        return $query->paginate($perPage ?? 15);
    }

    public function getByService(int $serviceId)
    {
        return Review::with([
            'appointmentService.service',
            'appointmentService.staff',
            'appointmentService.appointment.user',
        ])
            ->whereHas('appointmentService', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->where('status', 'approved')
            ->latest()
            ->get();
    }

    public function reviewStatus(int $serviceId, int $userId)
    {
        $apointmentService = AppointmentService::where('service_id', $serviceId)
            ->whereHas('appointment', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'completed');
            })
            ->whereDoesntHave('review')
            ->first();
        return $apointmentService;
    }

    public function getByUser(int $userId, int $perPage, ?string $status = null): LengthAwarePaginator
    {
        $query = Review::with('appointmentService.service:id,name')
            ->where('user_id', $userId)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    public function getByStaff(int $staffId, int $perPage): LengthAwarePaginator
    {
        return Review::with(['appointmentService.appointment.user:id,name', 'appointmentService.service:id,name'])
            ->whereHas('appointmentService.service', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->latest()
            ->paginate($perPage);
    }
}
