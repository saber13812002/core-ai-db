<?php

namespace Database\Factories;

use App\Models\VectorCollection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VectorCollection>
 */
class VectorCollectionFactory extends Factory
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
            'vector_db' => 'chromadb',
            'external_collection_id' => (string) Str::uuid(),
            'search_mode' => 'hybrid',
            'filter_criteria' => ['language' => 'fa'],
            'status' => 'building',
            'total_items' => 0,
            'is_active' => true,
        ];
    }

    public function ready(): static
    {
        return $this->state([
            'status' => 'ready',
            'total_items' => 50,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
