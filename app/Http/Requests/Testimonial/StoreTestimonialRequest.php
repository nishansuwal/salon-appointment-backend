<?php

namespace App\Http\Requests\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'message' => ['sometimes', 'required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
