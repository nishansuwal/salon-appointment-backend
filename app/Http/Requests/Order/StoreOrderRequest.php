<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Authorize request
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [

            'address_id' => ['required', 'exists:addresses,id'],

            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string', 'max:20'],

            'payment_method' => [
                'required',
                'in:COD,esewa,khalti,credit_card'
            ],

            'notes' => ['nullable', 'string'],

            /* Order Items */

            'items' => ['required', 'array', 'min:1'],

            'items.*.product_id' => [
                'required',
                'exists:products,id'
            ],

            'items.*.product_variant_id' => [
                'nullable',
                'exists:product_variants,id'
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],

            'items.*.price' => [
                'required',
                'numeric',
                'min:0'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one order item is required.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price.required' => 'Item price is required.',
        ];
    }
}
