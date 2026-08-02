<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'sometimes|string|max:255',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ];
    }
}
