<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:services,slug',
            'duration_minutes' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ];
    }
}
