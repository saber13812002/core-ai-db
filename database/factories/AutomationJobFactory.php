<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\AutomationAction;
use App\Models\AutomationJob;
use App\Models\JobBatch;
use App\Models\MasterPrompt;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationJob>
 */
class AutomationJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_id' => JobBatch::factory(),
            'source_file_id' => SourceFile::factory(),
            'action_id' => AutomationAction::factory(),
            'model_id' => AiModel::factory(),
            'prompt_id' => MasterPrompt::factory(),
            'status' => 'queued',
            'priority' => 5,
            'progress_percent' => 0,
            'estimated_cost_usd' => fake()->randomFloat(2, 0, 1),
            'estimated_duration_sec' => fake()->numberBetween(10, 600),
            'retry_count' => 0,
            'max_retries' => 3,
            'queued_at' => now(),
            'source' => 'api',
            'is_automatic' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'progress_percent' => 100,
            'started_at' => now()->subMinutes(2),
            'completed_at' => now(),
            'actual_cost_usd' => fake()->randomFloat(2, 0, 1),
            'actual_duration_sec' => fake()->numberBetween(5, 300),
            'token_count' => fake()->numberBetween(100, 20000),
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
            'error_message' => 'upstream timeout',
        ]);
    }

    public function automatic(): static
    {
        return $this->state([
            'source' => 'scheduler',
            'is_automatic' => true,
        ]);
    }

    public function manual(): static
    {
        return $this->state([
            'source' => 'api',
            'is_automatic' => false,
        ]);
    }
}
