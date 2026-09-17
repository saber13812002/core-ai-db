<?php

namespace Database\Factories;

use App\Models\ModelRelease;
use App\Models\TrainedModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ModelRelease>
 */
class ModelReleaseFactory extends Factory
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
            'version' => '1.0.0',
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'release_notes' => fake()->optional()->sentence(),
            'performance_summary' => ['overall_score' => fake()->randomFloat(2, 0, 100)],
            'deployed_at' => null,
            'retired_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'approved_by' => (string) Str::uuid(),
            'approved_at' => now(),
        ]);
    }

    public function deployed(): static
    {
        return $this->approved()->state(['deployed_at' => now()]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
