<?php

namespace App\Http\Requests\StaffAvailability;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'day_of_week' => 'required|in:mon,tue,wed,thu,fri,sat,sun',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }
}
