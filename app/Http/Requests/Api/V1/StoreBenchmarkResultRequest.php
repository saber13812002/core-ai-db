<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBenchmarkResultRequest extends FormRequest
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
            'benchmark_session_id' => ['required', 'uuid', 'exists:benchmark_sessions,id'],
            'candidate_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'candidate_cleaned_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'baseline_output_id' => ['nullable', 'uuid', 'exists:processed_outputs,id'],
            'baseline_cleaned_id' => ['nullable', 'uuid', 'exists:cleaned_outputs,id'],
            'ground_truth_id' => ['nullable', 'uuid', 'exists:human_ground_truth,id'],
            'overall_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quality_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'metrics' => ['nullable', 'array'],
            'comparison_details' => ['nullable', 'array'],
            'is_candidate_better' => ['nullable', 'boolean'],
            'improvement_percent' => ['nullable', 'numeric'],
            'judge_model_id' => ['nullable', 'integer', 'exists:models,id'],
            'judge_prompt_id' => ['nullable', 'uuid', 'exists:prompts,id'],
            'judge_raw_response' => ['nullable', 'array'],
            'human_validated' => ['boolean'],
            'human_validated_by' => ['nullable', 'uuid'],
            'human_validated_at' => ['nullable', 'date'],
        ];
    }
}
