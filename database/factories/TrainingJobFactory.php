<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\ServiceRegistry;
use App\Models\TrainingJob;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TrainingJob>
 */
class TrainingJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dataset_id' => Dataset::factory(),
            'service_id' => ServiceRegistry::factory(),
            'external_job_id' => (string) Str::uuid(),
            'base_model_code' => fake()->slug(2),
            'training_config' => ['epochs' => 3, 'learning_rate' => 0.001],
            'status' => 'queued',
            'progress_percent' => 0,
            'estimated_cost_usd' => null,
            'actual_cost_usd' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function running(): static
    {
        return $this->state([
            'status' => 'running',
            'progress_percent' => 50,
            'started_at' => now()->subMinute(5),
        ]);
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'progress_percent' => 100,
            'actual_cost_usd' => fake()->randomFloat(4, 1, 100),
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'error_message' => fake()->sentence(),
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
    }
}
