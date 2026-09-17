<?php

namespace Database\Factories;

use App\Models\AutomationJob;
use App\Models\ServiceCallLog;
use App\Models\ServiceRegistry;
use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceCallLog>
 */
class ServiceCallLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => ServiceRegistry::factory(),
            'source_file_id' => SourceFile::factory(),
            'job_id' => AutomationJob::factory(),
            'endpoint' => fake()->randomElement(['/v1/extract', '/v1/summarize', '/v1/clean']),
            'http_method' => fake()->randomElement(['POST', 'GET']),
            'request_payload' => ['file_ref' => fake()->uuid()],
            'response_payload' => ['status' => 'ok'],
            'http_status' => 200,
            'duration_ms' => fake()->numberBetween(50, 5000),
            'error_message' => null,
        ];
    }

    public function failed(): static
    {
        return $this->state([
            'http_status' => 500,
            'error_message' => fake()->sentence(),
        ]);
    }
}
