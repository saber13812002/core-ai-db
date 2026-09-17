<?php

namespace Database\Factories;

use App\Models\TrainedModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainedModel>
 */
class TrainedModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'training_job_id' => null,
            'name' => fake()->unique()->slug(2),
            'version' => '1.0.0',
            'model_type' => fake()->randomElement(['llm', 'embedder']),
            'base_model_code' => fake()->slug(2),
            'storage_path' => 'trained-models/'.fake()->uuid(),
            'service_endpoint' => 'http://localhost:8080/models/'.fake()->uuid(),
            'status' => 'ready',
            'metadata' => ['params' => fake()->numberBetween(1_000_000, 70_000_000_000)],
        ];
    }
}
