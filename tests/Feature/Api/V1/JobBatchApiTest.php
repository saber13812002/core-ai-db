<?php

namespace Tests\Feature\Api\V1;

use App\Models\AutomationJob;
use App\Models\JobBatch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobBatchApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return JobBatch::class;
    }

    protected function tableName(): string
    {
        return 'job_batches';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/job-batches';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['name' => 'batch-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['triggered_by' => 'scheduler'];
    }

    public function test_list_can_filter_by_status(): void
    {
        JobBatch::factory()->create(['status' => 'queued']);
        JobBatch::factory()->completed()->create();

        $this->getJson('api/v1/job-batches?status=completed')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'completed');
    }

    public function test_close_out_aggregates_job_actuals(): void
    {
        $batch = JobBatch::factory()->create();

        AutomationJob::factory()->completed()->count(2)->create(['batch_id' => $batch->id]);
        AutomationJob::factory()->failed()->create(['batch_id' => $batch->id]);

        $this->postJson("api/v1/job-batches/{$batch->id}/close-out")
            ->assertOk()
            ->assertJsonPath('data.status', 'partially_failed');

        $this->assertDatabaseHas('job_batches', [
            'id' => $batch->id,
            'actual_total_tokens' => (int) $batch->jobs()->sum('token_count'),
            'actual_duration_seconds' => (int) $batch->jobs()->sum('actual_duration_sec'),
        ]);
        $this->assertNotNull($batch->fresh()->completed_at);
    }

    public function test_close_out_all_completed_marks_completed(): void
    {
        $batch = JobBatch::factory()->create();

        AutomationJob::factory()->completed()->count(2)->create(['batch_id' => $batch->id]);

        $this->postJson("api/v1/job-batches/{$batch->id}/close-out")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    public function test_automation_job_batch_id_must_exist(): void
    {
        $job = AutomationJob::factory()->create();

        $this->patchJson("api/v1/jobs/{$job->id}", ['batch_id' => (string) Str::uuid()])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['batch_id']);
    }
}
