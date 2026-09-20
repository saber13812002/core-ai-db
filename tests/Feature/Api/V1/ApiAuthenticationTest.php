<?php

namespace Tests\Feature\Api\V1;

use App\Models\SourceFile;
use Illuminate\Support\Str;

class ApiAuthenticationTest extends ApiTestCase
{
    public function test_request_without_api_key_is_rejected_with_401(): void
    {
        $this->defaultHeaders = [];

        $this->getJson('api/v1/files')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'unauthenticated');
    }

    public function test_request_with_invalid_api_key_is_rejected_with_401(): void
    {
        $this->defaultHeaders = ['X-API-Key' => 'sk_live_'.Str::random(32)];

        $this->getJson('api/v1/files')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'invalid_api_key');
    }

    public function test_request_with_revoked_api_key_is_rejected_with_401(): void
    {
        $this->apiKey->update(['is_active' => false]);

        $this->getJson('api/v1/files')
            ->assertUnauthorized()
            ->assertJsonPath('error.code', 'invalid_api_key');
    }

    public function test_stored_resource_carries_calling_api_key_as_creator(): void
    {
        $file = SourceFile::factory()->make();

        $payload = $file->attributesToArray();
        unset($payload['id'], $payload['created_at'], $payload['updated_at'], $payload['deleted_at'], $payload['created_by']);
        $payload['external_ref'] = 'ref-creator-'.Str::lower(Str::random(10));

        $response = $this->postJson('api/v1/files', $payload)->assertCreated();

        $this->assertDatabaseHas('source_files', [
            'id' => $response->json('data.id'),
            'created_by' => $this->apiKey->id,
        ]);
    }

    public function test_request_with_valid_api_key_is_allowed_and_records_usage(): void
    {
        $this->getJson('api/v1/files')->assertOk();

        $this->assertNotNull($this->apiKey->fresh()->last_used_at);
    }

    public function test_bearer_authorization_header_is_accepted(): void
    {
        $this->defaultHeaders = [];
        $this->defaultHeaders['Authorization'] = 'Bearer '.$this->apiKeyPlain;

        $this->getJson('api/v1/files')->assertOk();
    }
}
