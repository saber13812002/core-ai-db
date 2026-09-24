<?php

namespace Tests\Feature\Api\V1;

use App\Models\AutomationJob;
use App\Models\IntegrationEvent;
use App\Models\SourceFile;
use Illuminate\Support\Str;

class IntegrationEventApiTest extends ApiTestCase
{
    public function test_webhook_stores_event_for_existing_reference(): void
    {
        $file = SourceFile::factory()->create();

        $response = $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'event_type' => 'started',
            'payload' => ['stage' => 'transcription'],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('integration_events', [
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'event_type' => 'started',
        ]);
    }

    public function test_webhook_rejects_missing_reference_row(): void
    {
        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'automation_job',
            'reference_id' => (string) Str::uuid(),
            'event_type' => 'completed',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'unknown_reference');
    }

    public function test_webhook_rejects_reference_id_on_wrong_type_table(): void
    {
        $file = SourceFile::factory()->create();

        // The UUID exists, but as a source_file — not as a job batch.
        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'job_batch',
            'reference_id' => $file->id,
            'event_type' => 'started',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'unknown_reference');
    }

    public function test_webhook_rejects_invalid_reference_type_enum(): void
    {
        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'invoice',
            'reference_id' => (string) Str::uuid(),
            'event_type' => 'started',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['reference_type']);
    }

    public function test_history_is_listed_for_reference(): void
    {
        $job = AutomationJob::factory()->create();
        IntegrationEvent::factory()->forJob($job)->create(['event_type' => 'started']);
        IntegrationEvent::factory()->forJob($job)->failed()->create();

        $this->getJson("api/v1/integration-events/automation_job/{$job->id}")
            ->assertOk()
            ->assertJsonPath('reference_type', 'automation_job')
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_reference_and_event_type(): void
    {
        $file = SourceFile::factory()->create();
        IntegrationEvent::factory()->create([
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'event_type' => 'failed',
        ]);
        IntegrationEvent::factory()->forJob(AutomationJob::factory()->create());

        $this->getJson('api/v1/webhooks/integration-events?reference_type=source_file&reference_id='.$file->id)
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson('api/v1/webhooks/integration-events?event_type=failed')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
