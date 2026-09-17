<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\CleanedOutput;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CleanedOutput>
 */
class CleanedOutputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraphs(2, true);
        $processedOutput = ProcessedOutput::factory()->create();

        return [
            'processed_output_id' => $processedOutput,
            'source_file_id' => $processedOutput->source_file_id,
            'cleaning_prompt_id' => MasterPrompt::factory(),
            'model_id' => AiModel::factory(),
            'content_text' => $content,
            'content_json' => null,
            'content_hash' => hash('sha256', $content),
            'cleaning_type' => 'normalize',
            'quality_score' => fake()->randomFloat(2, 0, 100),
            'is_human_approved' => false,
            'version_number' => 1,
            'is_latest' => true,
            'processing_metadata' => [],
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'is_human_approved' => true,
            'human_approved_by' => (string) Str::uuid(),
            'human_approved_at' => now(),
        ]);
    }
}
