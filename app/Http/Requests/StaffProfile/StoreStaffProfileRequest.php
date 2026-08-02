<?php

namespace App\Http\Requests\StaffProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'position' => [
                'required',
                'string',
                'max:255',
            ],

            'experience' => [
                'required',
                'integer',
                'min:0',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            'avg_rating' => [
                'nullable',
                'numeric',
                'min:0',
                'max:5',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'categories' => [
                'required',
                'array',
                'min:1',
            ],

            'categories.*' => [
                'integer',
                Rule::exists('categories', 'id')
                    ->where('level', 'main')
                    ->where('is_active', true),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Please select at least one category.',
            'categories.array' => 'Categories must be an array.',
            'categories.min' => 'Please select at least one category.',
            'categories.*.exists' => 'One or more selected categories are invalid.',
        ];
    }
}
