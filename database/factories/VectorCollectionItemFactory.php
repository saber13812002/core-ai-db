<?php

namespace Database\Factories;

use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use App\Models\VectorCollection;
use App\Models\VectorCollectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<VectorCollectionItem>
 */
class VectorCollectionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sourceFile = SourceFile::factory()->create();

        return [
            'vector_collection_id' => VectorCollection::factory(),
            'source_file_id' => $sourceFile,
            'processed_output_id' => ProcessedOutput::factory(),
            'cleaned_output_id' => null,
            'external_vector_id' => (string) Str::uuid(),
            'chunk_ref' => ['chunk_index' => 0, 'total_chunks' => 5],
            'metadata' => ['chunk_tokens' => fake()->numberBetween(100, 800)],
        ];
    }
}
