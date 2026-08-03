<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'image',
        'level',
        'slug',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category) {
            $category->slug = Str::slug($category->name);
            $category->level = $category->parent_id ? 'child' : 'main';
        });
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the children categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the services associated with this category.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(
            StaffProfile::class,
            'staff_category',
            'category_id',
            'staff_id'
        );
    }
}
