<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The table associated with the model.
     * (Optional if your table name follows Laravel conventions)
     */
    protected $table = 'settings';

    /**
     * Disable timestamps if you don't need them for settings.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'salon_name',
        'email',
        'phone',
        'address',
        'logo',
        'opening_time',
        'closing_time',
    ];

    /**
     * Helper to get the first (and only) settings record.
     * This avoids having to call Setting::first() everywhere.
     */
    public static function getConfiguration()
    {
        return self::first() ?? new self();
    }
}