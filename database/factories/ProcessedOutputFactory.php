<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\AutomationAction;
use App\Models\AutomationJob;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProcessedOutput>
 */
class ProcessedOutputFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraphs(3, true);
        $sourceFile = SourceFile::factory();

        return [
            'job_id' => AutomationJob::factory(),
            'source_file_id' => $sourceFile,
            'output_type_id' => OutputType::factory(),
            'action_id' => AutomationAction::factory(),
            'model_id' => AiModel::factory(),
            'prompt_id' => MasterPrompt::factory(),
            'content_text' => $content,
            'content_json' => null,
            'content_hash' => hash('sha256', $content),
            'chunk_refs' => [],
            'token_count' => fake()->numberBetween(100, 10000),
            'char_count' => mb_strlen($content),
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

    public function superseded(): static
    {
        return $this->state(['is_latest' => false]);
    }
}
