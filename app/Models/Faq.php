<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort_order.
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}