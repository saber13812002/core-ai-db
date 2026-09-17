<?php

namespace Database\Factories;

use App\Models\AiModel;
use App\Models\FeedbackLog;
use App\Models\MasterPrompt;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedbackLog>
 */
class FeedbackLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_file_id' => SourceFile::factory(),
            'processed_output_id' => ProcessedOutput::factory(),
            'cleaned_output_id' => null,
            'trained_model_id' => null,
            'prompt_id' => MasterPrompt::factory(),
            'model_id' => AiModel::factory(),
            'feedback_type' => fake()->randomElement(['like', 'dislike', 'correction', 'flag']),
            'user_id' => fake()->optional()->uuid(),
            'session_id' => fake()->optional()->uuid(),
            'comment' => fake()->optional()->sentence(),
            'context' => ['action' => fake()->randomElement(['extract', 'summarize', 'clean'])],
        ];
    }
}
