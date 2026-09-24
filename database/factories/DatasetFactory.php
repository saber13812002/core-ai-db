<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\OutputType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dataset>
 */
class DatasetFactory extends Factory
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
            'purpose' => fake()->randomElement(['fine-tuning', 'benchmark', 'evaluation']),
            'target_model_type' => fake()->randomElement(['llm', 'embedder']),
            'target_output_type_id' => OutputType::factory(),
            'filter_criteria' => ['min_quality_score' => 60],
            'status' => 'building',
            'version_number' => 1,
            'previous_dataset_id' => null,
            'total_items' => 0,
            'train_count' => 0,
            'validation_count' => 0,
            'test_count' => 0,
            'storage_path' => 'datasets/'.fake()->uuid(),
            'created_by' => null,
        ];
    }

    public function ready(): static
    {
        return $this->state([
            'status' => 'ready',
            'total_items' => 100,
            'train_count' => 80,
            'validation_count' => 10,
            'test_count' => 10,
        ]);
    }

    /**
     * A new version of a previously-existing dataset (git-like chain).
     */
    public function nextVersion(Dataset $previous): static
    {
        return $this->state([
            'version_number' => $previous->version_number + 1,
            'previous_dataset_id' => $previous->id,
        ]);
    }
}
