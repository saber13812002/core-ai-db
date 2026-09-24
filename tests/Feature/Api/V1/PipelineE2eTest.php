<?php

namespace Tests\Feature\Api\V1;

use App\Models\AiModel;
use App\Models\AutomationAction;
use App\Models\AutomationJob;
use App\Models\Dataset;
use App\Models\MasterPrompt;
use App\Models\MetadataSchema;
use App\Models\OutputType;
use App\Models\ProcessedOutput;
use App\Models\Project;
use App\Models\SourceFile;
use App\Models\SourceType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Black-box end-to-end scenarios: each test method exercises one full user
 * scenario through the public API only (no direct model writes except
 * where the scenario is about data *state*, e.g. a job already completed
 * by a worker that this system does not ship yet).
 *
 * Anchored 1:1 to plans/acceptance-e2e-backlog.md (E2E-01 .. E2E-08).
 */
class PipelineE2eTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    // ------------------------------------------------------------------
    // E2E-01 — Upload a Word/PDF/Excel/TXT/video/audio file via the web
    // service: it is registered in the DB, visible, downloadable, and its
    // metadata is stored as JSON key-value (with optional schema).
    // ------------------------------------------------------------------

    public function test_e2e_01_upload_registers_file_with_metadata_project_and_source_type(): void
    {
        $project = Project::factory()->create();
        $sourceType = SourceType::factory()->create(['code' => 'audio', 'label_fa' => 'صوت']);

        $upload = $this->post('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('lecture.mp3', 8, 'audio/mpeg'),
            'language' => 'fa',
            'project_id' => $project->id,
            'source_type_id' => $sourceType->id,
            'metadata' => ['speaker' => 'دکتر علی', 'topic' => 'فلسفه'],
        ]);

        $upload->assertCreated()
            ->assertJsonPath('data.file_type', 'mp3')
            ->assertJsonPath('data.processing_status', 'registered')
            ->assertJsonPath('data.created_by', $this->apiKey->id)
            ->assertJsonPath('data.metadata.speaker', 'دکتر علی')
            ->assertJsonPath('data.metadata.topic', 'فلسفه')
            ->assertJsonPath('data.project_id', $project->id)
            ->assertJsonPath('data.source_type_id', $sourceType->id);

        $id = $upload->json('data.id');

        // Registered row is visible via the API.
        $this->getJson("api/v1/files/{$id}")
            ->assertOk()
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.project.name', $project->name)
            ->assertJsonPath('data.source_type.code', 'audio');

        // The stored binary round-trips.
        $this->assertFileExists(Storage::disk('local')->path($upload->json('data.storage_path')));

        $this->get("/api/v1/files/{$id}/download")
            ->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=lecture.mp3');
    }

    public function test_e2e_02_all_seven_file_types_upload_and_register(): void
    {
        $cases = [
            ['course.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            ['book.pdf', 'application/pdf'],
            ['sheet.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            ['notes.txt', 'text/plain'],
            ['video.mp4', 'video/mp4'],
            ['voice.mp3', 'audio/mpeg'],
            ['voice.wav', 'audio/wav'],
        ];

        $stored = [];

        foreach ($cases as $index => [$name, $mime]) {
            $response = $this->post('/api/v1/files/upload', [
                'file' => UploadedFile::fake()
                    ->createWithContent($name, "e2e-distinct-content-{$index}")
                    ->mimeType($mime),
            ]);

            $response->assertCreated()
                ->assertJsonPath('data.file_type', pathinfo($name, PATHINFO_EXTENSION))
                ->assertJsonPath('data.processing_status', 'registered');

            $stored[] = $response->json('data');
        }

        // All seven are listed back through the API with their metadata.
        $this->getJson('api/v1/files?per_page=20')
            ->assertJsonCount(7, 'data');

        foreach ($stored as $row) {
            $this->assertNotNull($row['id']);
            $this->assertNotNull($row['checksum_sha256']);
        }
    }

    // ------------------------------------------------------------------
    // E2E-03 — Optional JSON schema enforcement on metadata: a schema for
    // the file type is applied; violations are 422; valid payloads pass;
    // files can later be found back by a metadata key.
    // ------------------------------------------------------------------

    public function test_e2e_03_metadata_schema_enforced_on_upload_and_queryable(): void
    {
        MetadataSchema::factory()->create([
            'scope' => 'mp3',
            'schema' => [
                'speaker' => ['type' => 'string', 'required' => true],
                'session' => ['type' => 'integer', 'required' => false],
            ],
        ]);

        // Missing the required key → 422.
        $this->post('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('no-speaker.mp3', 4, 'audio/mpeg'),
            'metadata' => ['topic' => 'x'],
        ])
            ->assertUnprocessable()
            ->assertJsonFragment(['metadata.speaker is required by the schema.']);

        // Wrong type → 422.
        $this->post('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('bad-type.mp3', 4, 'audio/mpeg'),
            'metadata' => ['speaker' => 42],
        ])
            ->assertUnprocessable()
            ->assertJsonFragment(['metadata.speaker must be a string.']);

        // Valid payload → 201, and findable by that metadata key.
        $upload = $this->post('/api/v1/files/upload', [
            'file' => UploadedFile::fake()->create('good.mp3', 4, 'audio/mpeg'),
            'metadata' => ['speaker' => 'محدث ثانی', 'session' => 3],
        ]);
        $upload->assertCreated();

        $this->getJson('api/v1/files?metadata[speaker]=محدث ثانی')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $upload->json('data.id'));
    }

    // ------------------------------------------------------------------
    // E2E-04 — Job status tracking: who sent it (created_by), when
    // (queued_at), which model, which action, and whether it was
    // automatic or manual (source + is_automatic), all queryable.
    // ------------------------------------------------------------------

    public function test_e2e_04_job_tracks_origin_model_action_and_automation(): void
    {
        $file = SourceFile::factory()->create();
        $action = AutomationAction::factory()->create();
        $model = AiModel::factory()->create();

        $manual = $this->postJson('api/v1/jobs', [
            'source_file_id' => $file->id,
            'action_id' => $action->id,
            'model_id' => $model->id,
            'source' => 'api',
            'is_automatic' => false,
        ]);
        $manual->assertCreated()
            ->assertJsonPath('data.source', 'api')
            ->assertJsonPath('data.is_automatic', false)
            ->assertJsonPath('data.status', 'queued');

        $automatic = $this->postJson('api/v1/jobs', [
            'source_file_id' => $file->id,
            'action_id' => $action->id,
            'model_id' => $model->id,
            'source' => 'scheduler',
            'is_automatic' => true,
        ]);
        $automatic->assertCreated()
            ->assertJsonPath('data.source', 'scheduler')
            ->assertJsonPath('data.is_automatic', true);

        // Queryable by source and automation flag.
        $this->getJson('api/v1/jobs?source=scheduler')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.is_automatic', true);

        $this->getJson('api/v1/jobs?is_automatic=0')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.source', 'api');

        // Show exposes the full status picture: model, action, when, who.
        $jobId = $manual->json('data.id');
        $jobData = $manual->json('data');

        $this->assertNotEmpty($jobData['queued_at']);

        $this->getJson("api/v1/jobs/{$jobId}")
            ->assertOk()
            ->assertJsonPath('data.model.id', $model->id)
            ->assertJsonPath('data.action.id', $action->id)
            ->assertJsonPath('data.queued_at', $jobData['queued_at']);

        // The row in the DB carries the same origin facts.
        $storedJob = AutomationJob::find($jobId);
        $this->assertSame($model->id, $storedJob->model_id);
        $this->assertSame($action->id, $storedJob->action_id);
        $this->assertFalse($storedJob->is_automatic);
    }

    // ------------------------------------------------------------------
    // E2E-05 — A queued job's result is stored in the DB as an output row
    // that carries the JSON result, the original file id, the action, and
    // the output type. Full audio chain: subtitle → corrected → full text
    // (multiple LLMs/prompts) → summary, all traceable to the one file.
    // ------------------------------------------------------------------

    public function test_e2e_05_audio_chain_results_are_stored_and_traceable(): void
    {
        $file = SourceFile::factory()->create(['file_type' => 'mp3']);

        // A deterministic stand-in for "the worker completed the job":
        // job rows with status=completed plus their ProcessedOutput rows.
        $transcriptJob = AutomationJob::factory()->completed()->create([
            'source_file_id' => $file->id,
            'action_id' => AutomationAction::factory()->create(['code' => 'extract-transcript']),
        ]);
        $subtitle = ProcessedOutput::factory()->forFile($file, $transcriptJob)->create([
            'output_type_id' => OutputType::factory()->create(['code' => 'lecture-transcript']),
            'content_json' => ['lines' => [['text' => 'بسمه تعالی', 'start' => 0, 'end' => 2]]],
        ]);

        // Corrected subtitle — a *different* model and prompt (multiple LLMs).
        $refineJob = AutomationJob::factory()->completed()->create([
            'source_file_id' => $file->id,
            'parent_job_id' => $transcriptJob->id,
            'action_id' => AutomationAction::factory()->create(['code' => 'refine-text']),
        ]);
        $corrected = ProcessedOutput::factory()->forFile($file, $refineJob)->create([
            'output_type_id' => OutputType::factory()->create(['code' => 'cleaned-text']),
        ]);

        // Summary from the corrected text.
        $summaryJob = AutomationJob::factory()->completed()->create([
            'source_file_id' => $file->id,
            'parent_job_id' => $refineJob->id,
            'action_id' => AutomationAction::factory()->create(['code' => 'summarize']),
        ]);
        $summary = ProcessedOutput::factory()->forFile($file, $summaryJob)->create([
            'output_type_id' => OutputType::factory()->create(['code' => 'summary']),
        ]);

        // The JSON result is stored on the subtitle row.
        $this->assertDatabaseHas('processed_outputs', [
            'id' => $subtitle->id,
            'source_file_id' => $file->id,
            'job_id' => $transcriptJob->id,
        ]);

        // Every output in the chain traces back to the single original file.
        $this->getJson('api/v1/outputs?source_file_id='.$file->id.'&per_page=20')
            ->assertOk()
            ->assertJsonCount(3, 'data');

        // Filter by output type isolates each stage.
        $this->getJson('api/v1/outputs?output_type_id='.$subtitle->output_type_id)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $subtitle->id);

        $this->getJson('api/v1/outputs?output_type_id='.$summary->output_type_id)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $summary->id);

        // The chain is linked job→job via parent_job_id.
        $this->assertSame($transcriptJob->id, $refineJob->fresh()->parent_job_id);
        $this->assertSame($refineJob->id, $summaryJob->fresh()->parent_job_id);
    }

    // ------------------------------------------------------------------
    // E2E-06 — A dataset is built from metadata-filtered outputs, then
    // versioned (git-like): v2 points back at v1.
    // ------------------------------------------------------------------

    public function test_e2e_06_dataset_built_from_filtered_outputs_and_versioned(): void
    {
        $file = SourceFile::factory()->create();
        $outputType = OutputType::factory()->create(['code' => 'cleaned-text']);

        // Two approved outputs for this file, filterable by output type.
        $out1 = ProcessedOutput::factory()->forFile($file)->approved()->create([
            'output_type_id' => $outputType->id,
        ]);
        $out2 = ProcessedOutput::factory()->forFile($file)->approved()->create([
            'output_type_id' => $outputType->id,
        ]);

        // A third output of a *different* type that must NOT be picked up.
        ProcessedOutput::factory()->forFile($file)->create([
            'output_type_id' => OutputType::factory()->create(['code' => 'summary'])->id,
        ]);

        $selected = $this->getJson('api/v1/outputs?output_type_id='.$outputType->id.'&per_page=20')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->json('data');

        // The selected ids are exactly the two approved cleaned-text outputs.
        $selectedIds = array_map(fn (array $row) => $row['id'], $selected);
        sort($selectedIds);
        $expectedIds = [$out1->id, $out2->id];
        sort($expectedIds);
        $this->assertSame($expectedIds, $selectedIds);

        // Build v1 of the dataset from the selected outputs.
        $v1 = $this->postJson('api/v1/datasets', [
            'name' => 'dataset-v1',
            'purpose' => 'fine-tuning',
            'target_output_type_id' => $outputType->id,
            'status' => 'ready',
            'filter_criteria' => ['output_type' => 'cleaned-text', 'human_approved' => true],
        ]);
        $v1->assertCreated()->assertJsonPath('data.version_number', 1);
        $datasetId = $v1->json('data.id');

        foreach ([$out1->id, $out2->id] as $n => $outputId) {
            $this->postJson('api/v1/dataset-items', [
                'dataset_id' => $datasetId,
                'source_file_id' => $file->id,
                'input_output_id' => $outputId,
                'split' => 'train',
                'sequence_order' => $n + 1,
            ])->assertCreated();
        }

        // The dataset's items are queryable by dataset id.
        $this->getJson("api/v1/dataset-items?dataset_id={$datasetId}")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        // v2 chains back to v1.
        $v2 = $this->postJson('api/v1/datasets', [
            'name' => 'dataset-v2',
            'purpose' => 'fine-tuning',
            'target_output_type_id' => $outputType->id,
            'status' => 'ready',
            'version_number' => 2,
            'previous_dataset_id' => $datasetId,
        ]);
        $v2->assertCreated()
            ->assertJsonPath('data.version_number', 2)
            ->assertJsonPath('data.previous_dataset_id', $datasetId);

        $this->getJson('api/v1/datasets/'.$v2->json('data.id'))
            ->assertJsonPath('data.previous_dataset.id', $datasetId);
    }

    // ------------------------------------------------------------------
    // E2E-07 — Fine-tuning with the dataset: the training job, the final
    // trained model, and the benchmark results (evaluation + per-metric
    // rows) all land in the DB and are readable back.
    // ------------------------------------------------------------------

    public function test_e2e_07_finetune_and_benchmark_results_are_stored(): void
    {
        $dataset = Dataset::factory()->ready()->create();

        $training = $this->postJson('api/v1/training-jobs', [
            'dataset_id' => $dataset->id,
            'base_model_code' => 'gpt-4o',
            'status' => 'completed',
            'training_config' => ['epochs' => 3, 'learning_rate' => 0.001],
        ]);
        $training->assertCreated();
        $trainingId = $training->json('data.id');

        $model = $this->postJson('api/v1/trained-models', [
            'training_job_id' => $trainingId,
            'name' => 'khorasani-llm',
            'version' => '1.0.0',
            'model_type' => 'llm',
            'status' => 'ready',
        ]);
        $model->assertCreated();
        $modelId = $model->json('data.id');

        $judge = AiModel::factory()->create();
        $judgePrompt = MasterPrompt::factory()->create();

        $evaluation = $this->postJson('api/v1/model-evaluations', [
            'trained_model_id' => $modelId,
            'evaluation_type' => 'benchmark',
            'overall_score' => 82.5,
            'judge_model_id' => $judge->id,
            'judge_prompt_id' => $judgePrompt->id,
        ]);
        $evaluation->assertCreated();
        $evaluationId = $evaluation->json('data.id');

        // Per-metric benchmark rows, nested under the evaluation.
        $this->postJson("api/v1/model-evaluations/{$evaluationId}/metrics", [
            'metric_name' => 'accuracy',
            'score' => 84.0,
            'judge_model_id' => $judge->id,
        ])->assertCreated();

        $this->postJson("api/v1/model-evaluations/{$evaluationId}/metrics", [
            'metric_name' => 'fluency',
            'score' => 79.5,
            'judge_model_id' => $judge->id,
        ])->assertCreated();

        // Everything is readable back through the API.
        $this->getJson("api/v1/model-evaluations/{$evaluationId}")
            ->assertOk()
            ->assertJsonPath('data.trained_model.id', $modelId)
            ->assertJsonPath('data.overall_score', '82.50')
            ->assertJsonCount(2, 'data.metric_rows');

        $this->getJson("api/v1/training-jobs/{$trainingId}")
            ->assertJsonPath('data.dataset_id', $dataset->id);
        $this->getJson("api/v1/trained-models/{$modelId}")
            ->assertJsonPath('data.training_job_id', $trainingId)
            ->assertJsonPath('data.status', 'ready');
    }

    // ------------------------------------------------------------------
    // E2E-08 — Integration events: external services report on files and
    // jobs via the webhook; the full history for a reference is replayable.
    // ------------------------------------------------------------------

    public function test_e2e_08_integration_events_record_and_replay_history(): void
    {
        $file = SourceFile::factory()->create();
        $job = AutomationJob::factory()->create(['source_file_id' => $file->id]);

        // External service reports on the file.
        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'event_type' => 'started',
            'payload' => ['stage' => 'transcription'],
        ])->assertCreated();

        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'source_file',
            'reference_id' => $file->id,
            'event_type' => 'completed',
            'payload' => ['stage' => 'transcription'],
        ])->assertCreated();

        // External service reports on the job.
        $this->postJson('api/v1/webhooks/integration-events', [
            'reference_type' => 'automation_job',
            'reference_id' => $job->id,
            'event_type' => 'failed',
            'payload' => ['error' => 'upstream timeout'],
        ])->assertCreated();

        // Replay each reference's full history.
        $this->getJson("api/v1/integration-events/source_file/{$file->id}")
            ->assertOk()
            ->assertJsonPath('reference_type', 'source_file')
            ->assertJsonCount(2, 'data');

        $this->getJson("api/v1/integration-events/automation_job/{$job->id}")
            ->assertJsonCount(1, 'data');
    }

    // ------------------------------------------------------------------
    // E2E-09 — Batch lifecycle: a batch of jobs is created with an
    // estimate, closed out, and the actuals aggregate back onto the batch.
    // ------------------------------------------------------------------

    public function test_e2e_09_batch_close_out_aggregates_actuals(): void
    {
        $batch = $this->postJson('api/v1/job-batches', [
            'name' => 'morning-batch',
            'estimated_total_tokens' => 100000,
            'estimated_duration_seconds' => 3600,
            'triggered_by' => 'scheduler',
        ]);
        $batch->assertCreated()->assertJsonPath('data.status', 'queued');
        $batchId = $batch->json('data.id');

        $file = SourceFile::factory()->create();
        $action = AutomationAction::factory()->create();

        AutomationJob::factory()->completed()->create([
            'batch_id' => $batchId,
            'source_file_id' => $file->id,
            'action_id' => $action->id,
            'token_count' => 1200,
            'actual_duration_sec' => 40,
        ]);
        AutomationJob::factory()->completed()->create([
            'batch_id' => $batchId,
            'source_file_id' => $file->id,
            'action_id' => $action->id,
            'token_count' => 800,
            'actual_duration_sec' => 25,
        ]);
        AutomationJob::factory()->failed()->create([
            'batch_id' => $batchId,
            'source_file_id' => $file->id,
            'action_id' => $action->id,
            'actual_duration_sec' => 10,
        ]);

        $closeOut = $this->postJson("api/v1/job-batches/{$batchId}/close-out");
        $closeOut->assertOk()
            ->assertJsonPath('data.status', 'partially_failed')
            ->assertJsonPath('data.actual_total_tokens', 2000)
            ->assertJsonPath('data.actual_duration_seconds', 75);

        $this->assertNotNull($closeOut->json('data.completed_at'));
    }
}
