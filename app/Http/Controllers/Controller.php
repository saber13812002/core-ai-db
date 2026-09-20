<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Fill created_by with the calling API key when the client did not
     * provide it explicitly.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function withCreator(Request $request, array $attributes, string $column = 'created_by'): array
    {
        if (! isset($attributes[$column]) && $request->attributes->has('api_key')) {
            $attributes[$column] = $request->attributes->get('api_key')->getKey();
        }

        return $attributes;
    }
}
