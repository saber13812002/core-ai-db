<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\DatasetItem;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DatasetItem>
 */
class DatasetItemFactory extends Factory
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
            'source_file_id' => SourceFile::factory(),
            'input_output_id' => ProcessedOutput::factory(),
            'input_cleaned_id' => null,
            'ground_truth_id' => null,
            'split' => fake()->randomElement(['train', 'validation', 'test']),
            'sequence_order' => fake()->numberBetween(1, 1000),
            'metadata' => [],
        ];
    }
}
