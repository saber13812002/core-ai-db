<?php

namespace Database\Factories;

use App\Models\AutomationAction;
use App\Models\HumanGroundTruth;
use App\Models\OutputType;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<HumanGroundTruth>
 */
class HumanGroundTruthFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraphs(2, true);

        return [
            'source_file_id' => SourceFile::factory(),
            'output_type_id' => OutputType::factory(),
            'action_id' => AutomationAction::factory(),
            'content_text' => $content,
            'content_json' => null,
            'approved_by' => (string) Str::uuid(),
            'approved_at' => now(),
            'approval_notes' => fake()->optional()->sentence(),
            'confidence_level' => fake()->randomElement(['high', 'medium', 'low']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
