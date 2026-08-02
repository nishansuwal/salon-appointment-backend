<?php

namespace App\Http\Requests\Faq;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateFaqRequest extends StoreFaqRequest
{
    use HasOptionalRules;
    public function rules(): array { return $this->optionalRules(parent::rules()); }
}
