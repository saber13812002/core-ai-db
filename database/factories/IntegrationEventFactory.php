<?php

namespace Database\Factories;

use App\Models\AutomationJob;
use App\Models\IntegrationEvent;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationEvent>
 */
class IntegrationEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = SourceFile::factory()->create();

        return [
            'service_id' => null,
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'external_job_id' => fake()->optional()->uuid(),
            'event_type' => fake()->randomElement(['status_update', 'started', 'completed', 'failed']),
            'payload' => ['status' => fake()->randomElement(['running', 'done', 'error'])],
            'received_at' => now(),
        ];
    }

    public function forJob(AutomationJob $job): static
    {
        return $this->state([
            'reference_type' => 'automation_job',
            'reference_id' => $job->id,
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'event_type' => 'failed',
            'payload' => ['error' => fake()->sentence()],
        ]);
    }
}
