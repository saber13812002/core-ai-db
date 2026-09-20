<?php

namespace Tests\Concerns;

use App\Models\ApiKey;
use Illuminate\Support\Str;

/**
 * Creates a per-test API key and applies it to every outgoing request
 * via $defaultHeaders, so the EnsureApiKey middleware is exercised for
 * real (including created_by auto-fill and idempotency binding).
 */
trait InteractsWithApiKeys
{
    protected ?ApiKey $apiKey = null;

    protected string $apiKeyPlain = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKeyPlain = 'sk_live_test_'.Str::lower(Str::random(32));
        $this->apiKey = ApiKey::factory()->create([
            'name' => 'test-'.Str::lower(Str::random(8)),
            'key_hash' => ApiKey::hashKey($this->apiKeyPlain),
        ]);

        $this->defaultHeaders['X-API-Key'] = $this->apiKeyPlain;
    }
}
