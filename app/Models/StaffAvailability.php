<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAvailability extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Get the staff profile that owns this availability.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    /**
     * Scope a query to filter by a specific day.
     */
    public function scopeOnDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }
}