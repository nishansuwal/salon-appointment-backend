<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLeave extends Model
{
    protected $fillable = [
        'staff_id',
        'start_date',
        'end_date',
        'leave_type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}