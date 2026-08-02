<?php

namespace App\Repositories\Appointment;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface AppointmentRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * Customer cancels only their own appointment.
     */
    public function cancelAppointment(
        int $userId,
        int|string $appointmentId
    ): Model;

    /**
     * Staff updates only appointments assigned to them.
     */
    public function staffUpdateStatus(
        int|string $appointmentId,
        string $status
    ): Model;

    /**
     * Admin can update any appointment.
     */
    public function adminUpdateStatus(
        int|string $appointmentId,
        string $status
    ): Model;

    /**
     * Get appointments assigned to a staff member.
     */
    public function staffAppointments(
        array $filters = []
    ): LengthAwarePaginator;

    /**
     * Get all appointments for admin.
     */
    public function getUserAppointments(
        array $filters = []
    ): LengthAwarePaginator;


    public function userShow(
        int|string $appointmentId
    ): Model;

    public function staffShow(
        int|string $appointmentId
    ): Model;
}
