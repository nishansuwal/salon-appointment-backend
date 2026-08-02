<?php

namespace App\Http\Requests\Gallery;

use App\Http\Requests\Concerns\HasOptionalRules;

class UpdateGalleryRequest extends StoreGalleryRequest
{
    use HasOptionalRules;
    public function rules(): array
    {
        return $this->optionalRules(parent::rules());
    }
}
