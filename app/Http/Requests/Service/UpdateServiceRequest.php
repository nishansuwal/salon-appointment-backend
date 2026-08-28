<?php

namespace App\Http\Requests\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
        $service = $this->route('service');

        return [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'nullable|string',

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('services', 'slug')
                    ->ignore($service->id),
            ],

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

            'images.*.id' => 'sometimes|exists:service_images,id',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'This service already exists.',
        ];
    }
}
