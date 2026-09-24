<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\SourceFile;
use App\Models\SourceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SourceFile>
 */
class SourceFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_ref' => fake()->unique()->slug(3),
            'file_type' => fake()->randomElement(['pdf', 'docx', 'mp3', 'mp4']),
            'original_filename' => fake()->unique()->slug(2).'.pdf',
            'storage_path' => 'files/'.fake()->uuid(),
            'mime_type' => 'application/pdf',
            'file_size_bytes' => fake()->numberBetween(1_000, 10_000_000),
            'checksum_sha256' => hash('sha256', fake()->uuid()),
            'duration_seconds' => null,
            'page_count' => fake()->optional()->numberBetween(1, 200),
            'language' => 'fa',
            'metadata' => [
                'instructor' => fake()->name(),
                'topic' => fake()->sentence(3),
                'project_title' => fake()->sentence(2),
                'document_type' => 'lecture',
            ],
            'human_approved' => false,
            'processing_status' => 'pending',
            'version_number' => 1,
            'is_latest' => true,
            'project_id' => null,
            'source_type_id' => null,
        ];
    }

    public function withProjectAndType(): static
    {
        return $this->state([
            'project_id' => Project::factory(),
            'source_type_id' => fn (array $attributes) => match ($attributes['file_type']) {
                'mp3', 'wav', 'm4a' => SourceType::firstOrCreate(['code' => 'audio'], ['label_fa' => 'صوت'])->id,
                'mp4', 'mkv' => SourceType::firstOrCreate(['code' => 'video'], ['label_fa' => 'ویدیو'])->id,
                default => SourceType::firstOrCreate(['code' => $attributes['file_type']], ['label_fa' => ucfirst($attributes['file_type'])])->id,
            },
        ]);
    }

    public function approved(): static
    {
        return $this->state([
            'human_approved' => true,
            'human_approved_by' => (string) Str::uuid(),
            'human_approved_at' => now(),
        ]);
    }

    public function processed(): static
    {
        return $this->state(['processing_status' => 'completed']);
    }
}
