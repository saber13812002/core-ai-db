<?php

namespace Database\Factories;

use App\Models\JobBatch;
use App\Models\MasterPrompt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobBatch>
 */
class JobBatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->optional()->words(3, true),
            'master_prompt_id' => MasterPrompt::factory(),
            'scheduled_for' => null,
            'estimated_total_tokens' => fake()->randomElement([100000, 500000, 1000000]),
            'estimated_duration_seconds' => fake()->randomElement([3600, 7200]),
            'actual_total_tokens' => null,
            'actual_duration_seconds' => null,
            'status' => 'queued',
            'triggered_by' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status' => 'completed',
            'actual_total_tokens' => fake()->numberBetween(90000, 1100000),
            'actual_duration_seconds' => fake()->numberBetween(3600, 8000),
            'completed_at' => now(),
        ]);
    }

    public function partial(): static
    {
        return $this->state(['status' => 'partially_failed']);
    }
}
