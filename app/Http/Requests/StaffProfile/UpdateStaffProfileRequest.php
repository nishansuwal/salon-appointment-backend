<?php

namespace App\Http\Requests\StaffProfile;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateStaffProfileRequest extends StoreStaffProfileRequest
{
    use HasOptionalRules;
    public function rules(): array { return $this->optionalRules(parent::rules()); }
}
