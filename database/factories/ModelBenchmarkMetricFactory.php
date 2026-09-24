<?php

namespace Database\Factories;

use App\Models\MasterPrompt;
use App\Models\ModelBenchmarkMetric;
use App\Models\ModelEvaluation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModelBenchmarkMetric>
 */
class ModelBenchmarkMetricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_evaluation_id' => ModelEvaluation::factory(),
            'metric_name' => fake()->randomElement(['accuracy', 'fluency', 'completeness', 'faithfulness']),
            'metric_prompt_id' => MasterPrompt::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'judge_model_id' => null,
            'details' => ['rationale' => fake()->sentence()],
            'evaluated_at' => now(),
        ];
    }
}
