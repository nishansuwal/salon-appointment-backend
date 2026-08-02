<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'appointment_code',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'subtotal',
        'discount',
        'total',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Customer who booked the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Services included in the appointment.
     */
    public function appointmentServices(): HasMany
    {
        return $this->hasMany(AppointmentService::class);
    }

    /**
     * Status history.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class);
    }

    /**
     * Customer review.
     */
    public function review(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
