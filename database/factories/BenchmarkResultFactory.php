<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\BenchmarkResult;
use App\Models\BenchmarkSession;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BenchmarkResult>
 */
class BenchmarkResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'benchmark_session_id' => BenchmarkSession::factory(),
            'candidate_output_id' => ProcessedOutput::factory(),
            'candidate_cleaned_id' => null,
            'baseline_output_id' => ProcessedOutput::factory(),
            'baseline_cleaned_id' => null,
            'ground_truth_id' => null,
            'overall_score' => fake()->randomFloat(2, 0, 100),
            'quality_rate' => fake()->randomFloat(2, 0, 100),
            'metrics' => ['accuracy' => fake()->randomFloat(2, 0, 100)],
            'comparison_details' => ['reason' => fake()->sentence()],
            'is_candidate_better' => fake()->boolean(),
            'improvement_percent' => fake()->randomFloat(2, -50, 50),
            'judge_model_id' => AiModel::factory(),
            'judge_prompt_id' => MasterPrompt::factory(),
            'judge_raw_response' => null,
            'human_validated' => false,
            'human_validated_by' => null,
            'human_validated_at' => null,
        ];
    }

    public function humanValidated(): static
    {
        return $this->state([
            'human_validated' => true,
            'human_validated_by' => (string) Str::uuid(),
            'human_validated_at' => now(),
        ]);
    }
}
