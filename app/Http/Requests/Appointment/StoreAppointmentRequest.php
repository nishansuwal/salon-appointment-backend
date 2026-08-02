<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',

            'appointment_services' => 'required|array|min:1',

            'appointment_services.*.service_id' => [
                'required',
                'exists:services,id',
            ],

            'appointment_services.*.staff_id' => [
                'required',
                'exists:staff_profiles,id',
            ],

            'appointment_services.*.notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
