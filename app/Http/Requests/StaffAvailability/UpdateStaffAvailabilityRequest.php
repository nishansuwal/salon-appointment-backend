<?php

namespace App\Http\Requests\StaffAvailability;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateStaffAvailabilityRequest extends StoreStaffAvailabilityRequest
{
    use HasOptionalRules;
    public function rules(): array { return $this->optionalRules(parent::rules()); }
}
