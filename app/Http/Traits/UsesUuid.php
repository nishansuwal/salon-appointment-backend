<?php

namespace App\Http\Traits;

trait UsesUuid
{
     protected static function bootUsesUuid(): void
    {
        static::creating(function ($model) {
            $model->uid = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
