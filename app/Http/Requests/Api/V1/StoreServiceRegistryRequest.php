<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRegistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'service_type' => ['required', 'string', 'max:50'],
            'base_url' => ['nullable', 'string'],
            'api_key_ref' => ['nullable', 'string', 'max:200'],
            'endpoints' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'health_status' => ['nullable', 'string', 'max:50'],
            'last_health_check' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
