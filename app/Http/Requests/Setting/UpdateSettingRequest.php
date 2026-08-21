<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'bio' => ['nullable', 'string', 'max:255'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
        ];
    }
}
