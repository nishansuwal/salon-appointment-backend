<?php

namespace App\Http\Requests\Service;

use Illuminate\Support\Str;
use App\Models\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
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
            'discount' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:active,inactive',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_sort_order' => 'nullable|array',
            'image_sort_order.*' => 'nullable|integer|min:0',
            'image_is_primary' => 'nullable|array',
            'image_is_primary.*' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This service already exists.',
        ];
    }
}
