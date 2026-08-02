<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use App\Models\Service;
use App\Models\Appointment;

trait HandlesAppointmentCalculation
{
    /**
     * Generate a unique appointment code.
     */
    protected function generateAppointmentCode(): string
    {
        do {
            $code = 'APT-' . now()->format('YmdHis') . random_int(1000, 9999);
        } while (
            Appointment::where('appointment_code', $code)
            ->exists()
        );

        return $code;
    }

    /**
     * Calculate appointment end time.
     */
    protected function calculateEndTime(
        array $services,
        string $startTime
    ): string {
        $minutes = 0;

        foreach ($services as $service) {
            $minutes += Service::findOrFail(
                $service['service_id']
            )->duration_minutes;
        }

        return Carbon::parse($startTime)
            ->addMinutes($minutes)
            ->format('H:i');
    }
}
