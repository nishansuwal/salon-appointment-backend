<?php

namespace App\Http\Requests\StaffLeave;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffLeaveRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [    
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type' => 'required|in:annual,sick,emergency,unpaid,other',
            'reason' => 'nullable|string',
        ];
    }
}
