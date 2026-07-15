<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'position',
        'specialization',
        'avg_rating',
        'experience',
        'bio',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'avg_rating' => 'float',
        'experience' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user account associated with this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recurring weekly availability for this staff member.
     */
    public function availability(): HasMany
    {
        return $this->hasMany(StaffAvailability::class, 'staff_id');
    }

    /**
     * Get the specific leave dates for this staff member.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(StaffLeave::class, 'staff_id');
    }

    /**
     * Scope a query to only include active staff.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}