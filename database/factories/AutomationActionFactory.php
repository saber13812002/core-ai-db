<?php

namespace Database\Factories;

use App\Models\AutomationAction;
use App\Models\OutputType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationAction>
 */
class AutomationActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->slug(2),
            'name_fa' => fake()->sentence(3),
            'description' => fake()->optional()->sentence(),
            'action_category' => fake()->randomElement(['extract', 'summarize', 'translate', 'classify']),
            'input_file_types' => ['pdf', 'docx'],
            'output_type_id' => OutputType::factory(),
            'requires_prompt' => fake()->boolean(60),
            'is_batchable' => true,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
