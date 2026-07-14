<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait ResolvesIndexFiltersTrait
{
    protected function getIndexFilters(Request $request): array
    {
        return [
            'perPage' => (int) $request->input('pageSize', 10),
            'search'  => $request->input('searchValue', ''),
            'sort'    => in_array(
                $request->input('sort'),
                ['asc', 'desc'],
                true
            )
                ? $request->input('sort')
                : 'desc',
            'column'  => $request->input('column', 'id'),
        ];
    }
}
