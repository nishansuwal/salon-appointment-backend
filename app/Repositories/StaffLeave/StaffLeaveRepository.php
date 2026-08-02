<?php

namespace App\Repositories\StaffLeave;

use App\Models\StaffLeave;
use App\Models\Appointment;
use App\Repositories\AbstractCrudRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffLeaveRepository extends AbstractCrudRepository implements StaffLeaveRepositoryInterface
{
    protected string $modelClass = StaffLeave::class;

    public function staffLeaves(
        array $filters = []
    ): LengthAwarePaginator {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return $this->query()
                ->with([
                    'staff.user:id,name,email',
                    'approver:id,name,email',
                ])
                ->latest()
                ->paginate($filters['perPage']);
        } else {
            $staffId = $user->staffProfile->id;
            return $this->query()
                ->where('staff_id', $staffId)
                ->with([
                    'staff.user:id,name,email',
                    'approver:id,name,email',
                ])
                ->latest()
                ->paginate($filters['perPage']);
        }
    }

    /**
     * Staff applies for leave.
     */
    public function applyLeave(array $data): Model
    {
        $user = Auth::user();

        $staff = $user->staffProfile;

        if (!$staff) {
            throw ValidationException::withMessages([
                'staff' => 'Staff profile not found.',
            ]);
        }

        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->startOfDay();

        $this->validateLeaveDates($startDate, $endDate);

        /*
         * A staff member cannot create a leave
         * that overlaps another pending/approved leave.
         */
        $this->ensureNoOverlappingLeave(
            staffId: $staff->id,
            startDate: $startDate,
            endDate: $endDate
        );

        /*
         * Staff cannot apply for leave if they already
         * have appointments during the requested period.
         */
        $this->ensureNoAppointments(
            staffId: $staff->id,
            startDate: $startDate,
            endDate: $endDate
        );

        return StaffLeave::create([
            'staff_id' => $staff->id,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'leave_type' => $data['leave_type'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Admin approves a pending leave.
     */
    public function approveLeave(
        int $leaveId,
        int $adminUserId
    ): Model {
        return DB::transaction(function () use (
            $leaveId,
            $adminUserId
        ) {
            $leave = $this->findOrFail($leaveId);

            if ($leave->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Only pending leaves can be approved.',
                ]);
            }

            $startDate = Carbon::parse($leave->start_date)->startOfDay();
            $endDate = Carbon::parse($leave->end_date)->startOfDay();

            /*
             * Re-check appointments because an appointment
             * could have been created after the staff applied.
             */
            $this->ensureNoAppointments(
                staffId: $leave->staff_id,
                startDate: $startDate,
                endDate: $endDate
            );

            /*
             * Re-check overlapping approved/pending leaves.
             *
             * This protects against another leave being
             * created after this leave was submitted.
             */
            $overlappingLeave = StaffLeave::query()
                ->where('staff_id', $leave->staff_id)
                ->where('id', '!=', $leave->id)
                ->whereIn('status', [
                    'pending',
                    'approved',
                ])
                ->where(function ($query) use (
                    $startDate,
                    $endDate
                ) {
                    $query
                        ->whereDate(
                            'start_date',
                            '<=',
                            $endDate->toDateString()
                        )
                        ->whereDate(
                            'end_date',
                            '>=',
                            $startDate->toDateString()
                        );
                })
                ->exists();

            if ($overlappingLeave) {
                throw ValidationException::withMessages([
                    'leave' => 'This leave overlaps another staff leave.',
                ]);
            }

            $leave->update([
                'status' => 'approved',
                'approved_by' => $adminUserId,
                'approved_at' => now(),
            ]);

            return $leave->refresh();
        });
    }

    /**
     * Validate leave date range.
     */
    protected function validateLeaveDates(
        Carbon $startDate,
        Carbon $endDate
    ): void {
        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'End date must be after or equal to start date.',
            ]);
        }
    }

    /**
     * Ensure staff has no overlapping leave.
     */
    protected function ensureNoOverlappingLeave(
        int $staffId,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $exists = StaffLeave::query()
            ->where('staff_id', $staffId)
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->where(function ($query) use (
                $startDate,
                $endDate
            ) {
                $query
                    ->whereDate(
                        'start_date',
                        '<=',
                        $endDate->toDateString()
                    )
                    ->whereDate(
                        'end_date',
                        '>=',
                        $startDate->toDateString()
                    );
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_date' => 'You already have a leave during this period.',
            ]);
        }
    }

    /**
     * Ensure staff has no appointments during leave.
     */
    protected function ensureNoAppointments(
        int $staffId,
        Carbon $startDate,
        Carbon $endDate
    ): void {
        $exists = Appointment::query()
            ->whereBetween('appointment_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->whereHas(
                'appointmentServices',
                function ($query) use ($staffId) {
                    $query->where('staff_id', $staffId);
                }
            )
            ->whereNotIn('status', [
                'cancelled',
            ])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'leave' => 'The staff has appointments during this leave period.',
            ]);
        }
    }


    /**
     * Admin rejects leave.
     */
    public function rejectLeave(
        int $leaveId,
        int $adminUserId
    ): Model {

        $leave = $this->findOrFail($leaveId);

        if ($leave->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Only pending leaves can be rejected.',
            ]);
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => $adminUserId,
            'approved_at' => now(),
        ]);

        return $leave->refresh();
    }
}
