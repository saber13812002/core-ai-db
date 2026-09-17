<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\MasterPrompt;
use App\Models\ModelEvaluation;
use App\Models\TrainedModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModelEvaluation>
 */
class ModelEvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trained_model_id' => TrainedModel::factory(),
            'benchmark_session_id' => null,
            'evaluation_type' => fake()->randomElement(['benchmark', 'automated', 'human-review']),
            'overall_score' => fake()->randomFloat(2, 0, 100),
            'metrics' => ['accuracy' => fake()->randomFloat(2, 0, 100)],
            'baseline_model_id' => null,
            'improvement_percent' => null,
            'is_better_than_baseline' => null,
            'judge_model_id' => AiModel::factory(),
            'judge_prompt_id' => MasterPrompt::factory(),
            'details' => ['summary' => fake()->sentence()],
        ];
    }
}
