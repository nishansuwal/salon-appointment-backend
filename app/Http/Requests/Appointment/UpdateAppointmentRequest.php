<?php

namespace App\Http\Requests\Appointment;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateAppointmentRequest extends StoreAppointmentRequest
{
    use HasOptionalRules;
    public function rules(): array { return $this->optionalRules(parent::rules()); }
}
