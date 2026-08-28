<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'duration_minutes',
        'price',
        'discount',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'duration_minutes' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'staff',
    ];

    /**
     * Get the category that owns the service.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the service.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class);
    }

    public function getStaffAttribute()
    {
        if (!$this->category) {
            return collect();
        }

        // Service belongs to child category
        if ($this->category->parent_id) {
            return $this->category->parent?->staff ?? collect();
        }

        // Service belongs directly to main category
        return $this->category->staff;
    }
}
