<?php

namespace Database\Factories;

use App\Models\ApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApiKey>
 */
class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    /**
     * Define the model's default state.
     *
     * Tests that need the plaintext key create it explicitly:
     * ApiKey::factory()->create(['key_hash' => ApiKey::hashKey($plain)])
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->slug(2).'-key',
            'key_hash' => ApiKey::hashKey('sk_live_'.Str::lower(Str::random(32))),
            'scopes' => null,
            'is_active' => true,
            'last_used_at' => null,
            'created_by' => null,
        ];
    }
}
