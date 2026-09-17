<?php

namespace Database\Factories;

use App\Models\ModelRelease;
use App\Models\ReleaseReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReleaseReport>
 */
class ReleaseReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'release_id' => ModelRelease::factory(),
            'report_type' => fake()->randomElement(['benchmark', 'comparison', 'summary']),
            'content' => ['summary' => fake()->sentence(), 'score' => fake()->randomFloat(2, 0, 100)],
            'storage_path' => 'reports/'.fake()->uuid(),
        ];
    }
}
