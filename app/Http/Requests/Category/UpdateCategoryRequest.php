<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:25',
                Rule::unique('categories', 'name')->ignore($category?->id),
            ],
            'parent_id' => [
                'sometimes',
                'nullable',
                Rule::exists('categories', 'id')->where('level', 'main'),
                Rule::notIn([$category?->id]),
            ],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
