<?php

namespace App\Services;

use App\Exceptions\IntegrationEventReferenceException;
use App\Models\AutomationJob;
use App\Models\CleanedOutput;
use App\Models\Dataset;
use App\Models\IntegrationEvent;
use App\Models\JobBatch;
use App\Models\ProcessedOutput;
use App\Models\SourceFile;
use App\Models\TrainedModel;
use App\Repositories\IntegrationEventRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IntegrationEventService extends BaseService
{
    public function __construct(IntegrationEventRepository $repository)
    {
        parent::__construct($repository);
    }

    public function listWithFilters(?string $referenceType = null, ?string $referenceId = null, ?string $eventType = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listWithFilters($referenceType, $referenceId, $eventType, $perPage);
    }

    /**
     * Record a callback received from an external service. The reference
     * must resolve to an existing row of the named main table, so stale
     * callbacks are rejected instead of stored as orphans.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function record(array $attributes): IntegrationEvent
    {
        $type = $attributes['reference_type'];
        $id = $attributes['reference_id'];

        $class = match ($type) {
            'source_file' => SourceFile::class,
            'automation_job' => AutomationJob::class,
            'processed_output' => ProcessedOutput::class,
            'cleaned_output' => CleanedOutput::class,
            'dataset' => Dataset::class,
            'trained_model' => TrainedModel::class,
            'job_batch' => JobBatch::class,
            default => throw new IntegrationEventReferenceException("Unknown reference_type [{$type}]."),
        };

        if (! $class::whereKey($id)->exists()) {
            throw new IntegrationEventReferenceException("Reference [{$type}:{$id}] does not exist.");
        }

        return $this->repository->create($attributes);
    }

    /**
     * @return array<int, IntegrationEvent>
     */
    public function forReference(string $referenceType, string $referenceId): array
    {
        return $this->repository->forReference($referenceType, $referenceId);
    }
}
