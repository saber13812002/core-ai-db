<?php

namespace Database\Factories;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiModel>
 */
class AiModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->slug(2),
            'name_fa' => fake()->optional()->sentence(2),
            'provider' => fake()->randomElement(['openai', 'anthropic', 'google', 'local']),
            'model_type' => fake()->randomElement(['llm', 'embedding', 'asr', 'ocr']),
            'version' => fake()->optional()->semver(),
            'endpoint' => fake()->optional()->url(),
            'context_window' => fake()->numberBetween(4096, 128000),
            'is_active' => true,
            'metadata' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
