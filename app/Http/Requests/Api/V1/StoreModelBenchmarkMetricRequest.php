<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreModelBenchmarkMetricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     *
     * Note: model_evaluation_id is bound from the route, not the payload.
     */
    public function rules(): array
    {
        return [
            'metric_name' => ['required', 'string', 'max:100'],
            'metric_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'judge_model_id' => ['nullable', 'integer', 'exists:models,id'],
            'details' => ['nullable', 'array'],
            'evaluated_at' => ['nullable', 'date'],
        ];
    }
}
