<?php

namespace Database\Factories;

use App\Models\SourceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SourceType>
 */
class SourceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = fake()->unique()->word();

        return [
            'code' => $code,
            'label_fa' => fake()->optional()->word(),
            'description' => null,
            'is_active' => true,
        ];
    }

    public function audio(): static
    {
        return $this->state(['code' => Str::random(8).'-audio', 'label_fa' => 'صوت']);
    }

    public function pdf(): static
    {
        return $this->state(['code' => Str::random(8).'-pdf', 'label_fa' => 'PDF']);
    }
}
