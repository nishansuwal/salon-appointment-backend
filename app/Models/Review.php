<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'appointment_id',
        'user_id',
        'rating',
        'comment',
        'admin_note',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the appointment that this review belongs to.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the user who left the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}