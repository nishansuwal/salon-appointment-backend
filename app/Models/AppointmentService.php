<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AppointmentService extends Model
{
    protected $fillable = [
        'appointment_id',
        'service_id',
        'staff_id',
        'price',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    /**
     * Get the appointment that owns the service.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the service details.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the staff assigned to this service.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
