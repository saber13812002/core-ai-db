<?php

namespace Database\Factories;

use App\Models\AutomationAction;
use App\Models\AutomationFlow;
use App\Models\OutputType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationFlow>
 */
class AutomationFlowFactory extends Factory
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
            'platform' => fake()->randomElement(['n8n', 'zapier', 'make']),
            'platform_flow_id' => fake()->optional()->uuid(),
            'description' => fake()->optional()->sentence(),
            'input_file_types' => ['pdf'],
            'output_type_id' => OutputType::factory(),
            'action_id' => AutomationAction::factory(),
            'config' => [],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
