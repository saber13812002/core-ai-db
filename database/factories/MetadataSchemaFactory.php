<?php

namespace Database\Factories;

use App\Models\MetadataSchema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MetadataSchema>
 */
class MetadataSchemaFactory extends Factory
{
    protected $model = MetadataSchema::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->slug(3),
            'scope' => 'global',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
                'language' => ['type' => 'string', 'required' => false],
            ],
            'is_active' => true,
        ];
    }
}
