<?php

namespace Database\Factories;

use App\Models\AutomationAction;
use App\Models\MasterPrompt;
use App\Models\OutputType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MasterPrompt>
 */
class MasterPromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraph();

        return [
            'family_id' => (string) Str::uuid(),
            'name' => fake()->unique()->slug(3),
            'version' => '1.0',
            'content' => $content,
            'content_hash' => hash('sha256', $content),
            'prompt_type' => fake()->randomElement(['extraction', 'summarization', 'cleaning', 'judging']),
            'purpose' => fake()->optional()->sentence(),
            'target_output_type_id' => OutputType::factory(),
            'target_action_id' => AutomationAction::factory(),
            'parent_prompt_id' => null,
            'is_active' => true,
            'tags' => fake()->words(3),
            'metadata' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
