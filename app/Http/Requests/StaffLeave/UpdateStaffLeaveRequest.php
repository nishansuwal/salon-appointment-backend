<?php

namespace App\Http\Requests\StaffLeave;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateStaffLeaveRequest extends StoreStaffLeaveRequest
{
    use HasOptionalRules;
    public function rules(): array { return $this->optionalRules(parent::rules()); }
}
