<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentStatusHistory extends Model
{
    public $timestamps = false; 

    protected $fillable = [
        'appointment_id',
        'old_status',
        'new_status',
        'changed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Get the appointment associated with this history log.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}