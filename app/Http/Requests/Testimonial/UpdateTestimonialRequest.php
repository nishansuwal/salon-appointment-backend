<?php

namespace App\Http\Requests\Testimonial;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateTestimonialRequest extends StoreTestimonialRequest
{
    use HasOptionalRules;
    public function rules(): array
    {
        return $this->optionalRules(parent::rules());
    }
}
