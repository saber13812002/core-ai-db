<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\AutomationAction;
use App\Models\BenchmarkSession;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BenchmarkSession>
 */
class BenchmarkSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(3),
            'description' => fake()->optional()->sentence(),
            'benchmark_type' => fake()->randomElement(['ab', 'vs-ground-truth']),
            'source_file_id' => SourceFile::factory(),
            'output_type_id' => OutputType::factory(),
            'action_id' => AutomationAction::factory(),
            'judge_model_id' => AiModel::factory(),
            'judge_prompt_id' => MasterPrompt::factory(),
            'status' => 'pending',
            'total_items' => 10,
            'processed_items' => 0,
            'metadata' => [],
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'completed_at' => now(),
            'processed_items' => 10,
        ]);
    }
}
