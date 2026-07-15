<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
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
        'total_price' => 'decimal:2',
    ];

    /**
     * Customer who booked the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff assigned to the appointment.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
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