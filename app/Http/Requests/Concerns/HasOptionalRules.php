<?php

namespace App\Http\Requests\Concerns;

trait HasOptionalRules
{
    protected function optionalRules(array $rules): array
    {
        return collect($rules)->map(function ($rule) {
            $rules = is_array($rule) ? $rule : explode('|', $rule);
            $rules = array_values(array_filter($rules, fn ($item) => $item !== 'required'));
            array_unshift($rules, 'sometimes');

            return $rules;
        })->all();
    }
}
