<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreModelEvaluationRequest extends FormRequest
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
            'trained_model_id' => ['required', 'uuid', 'exists:trained_models,id'],
            'benchmark_session_id' => ['nullable', 'uuid', 'exists:benchmark_sessions,id'],
            'evaluation_type' => ['nullable', 'string', 'max:50'],
            'overall_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'metrics' => ['nullable', 'array'],
            'baseline_model_id' => ['nullable', 'uuid', 'exists:trained_models,id'],
            'improvement_percent' => ['nullable', 'numeric'],
            'is_better_than_baseline' => ['nullable', 'boolean'],
            'judge_model_id' => ['nullable', 'integer', 'exists:models,id'],
            'judge_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'details' => ['nullable', 'array'],
        ];
    }
}
