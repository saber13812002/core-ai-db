<?php

namespace App\Http\Resources\Api\V1;

use App\Models\BenchmarkResult;
use App\Models\BenchmarkSession;
use App\Models\CleanedOutput;
use App\Models\HumanGroundTruth;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BenchmarkResult
 */
class BenchmarkResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'benchmark_session_id' => $this->benchmark_session_id,
            'benchmark_session' => $this->whenLoaded('benchmarkSession', fn (BenchmarkSession $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
                'benchmark_type' => $rel->benchmark_type,
            ]),
            'candidate_output_id' => $this->candidate_output_id,
            'candidate_output' => $this->whenLoaded('candidateOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'candidate_cleaned_id' => $this->candidate_cleaned_id,
            'candidate_cleaned' => $this->whenLoaded('candidateCleaned', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'baseline_output_id' => $this->baseline_output_id,
            'baseline_output' => $this->whenLoaded('baselineOutput', fn (ProcessedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'baseline_cleaned_id' => $this->baseline_cleaned_id,
            'baseline_cleaned' => $this->whenLoaded('baselineCleaned', fn (CleanedOutput $rel): array => [
                'id' => $rel->id,
                'version_number' => $rel->version_number,
            ]),
            'ground_truth_id' => $this->ground_truth_id,
            'ground_truth' => $this->whenLoaded('groundTruth', fn (HumanGroundTruth $rel): array => [
                'id' => $rel->id,
                'output_type_id' => $rel->output_type_id,
            ]),
            'overall_score' => $this->overall_score,
            'quality_rate' => $this->quality_rate,
            'metrics' => $this->metrics,
            'comparison_details' => $this->comparison_details,
            'is_candidate_better' => $this->is_candidate_better,
            'improvement_percent' => $this->improvement_percent,
            'judge_model_id' => $this->judge_model_id,
            'judge_model' => $this->whenLoaded('judgeModel', fn ($rel): array => [
                'id' => $rel->id,
                'code' => $rel->code,
            ]),
            'judge_prompt_id' => $this->judge_prompt_id,
            'judge_prompt' => $this->whenLoaded('judgePrompt', fn (MasterPrompt $rel): array => [
                'id' => $rel->id,
                'name' => $rel->name,
            ]),
            'judge_raw_response' => $this->judge_raw_response,
            'human_validated' => $this->human_validated,
            'human_validated_by' => $this->human_validated_by,
            'human_validated_at' => $this->human_validated_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
