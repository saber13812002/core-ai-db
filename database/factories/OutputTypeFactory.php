<?php

namespace Database\Factories;

use App\Models\OutputType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutputType>
 */
class OutputTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schema = fake()->boolean(50) ? [
            'type' => 'object',
            'properties' => array_fill(0, fake()->numberBetween(1, 3), ['type' => 'string']),
        ] : null;

        return [
            'code' => fake()->unique()->slug(2),
            'name_fa' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'output_format' => fake()->randomElement(['json', 'text', 'markdown', 'csv']),
            'json_schema' => $schema,
            'supports_chunk' => fake()->boolean(30),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
