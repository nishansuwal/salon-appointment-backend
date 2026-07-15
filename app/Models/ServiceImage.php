<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    // Disable timestamps if not needed, as these are usually managed by the service parent
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'service_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the service that this image belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope to easily find the primary image for a service.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}