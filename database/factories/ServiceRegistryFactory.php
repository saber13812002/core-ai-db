<?php

namespace Database\Factories;

use App\Models\ServiceRegistry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRegistry>
 */
class ServiceRegistryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'service_type' => fake()->randomElement(['llm', 'asr', 'ocr', 'embedding']),
            'base_url' => fake()->url(),
            'api_key_ref' => fake()->optional()->slug(3),
            'endpoints' => ['complete' => '/v1/completions'],
            'is_active' => true,
            'health_status' => 'healthy',
            'last_health_check' => now(),
            'metadata' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
