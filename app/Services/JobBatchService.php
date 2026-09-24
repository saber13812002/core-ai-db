<?php

namespace App\Services;

use App\Models\JobBatch;
use App\Repositories\JobBatchRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class JobBatchService extends BaseService
{
    public function __construct(JobBatchRepository $repository)
    {
        parent::__construct($repository);
    }

    public function listWithFilters(?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listWithFilters($status, $perPage);
    }

    /**
     * Create a batch and, in the same transaction, record its estimated
     * cost (computed from the job count when not supplied explicitly).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function createWithEstimate(array $attributes): JobBatch
    {
        $attributes['estimated_total_tokens'] ??= 0;
        $attributes['estimated_duration_seconds'] ??= 0;

        return $this->repository->create($attributes);
    }

    /**
     * Aggregate the jobs' actuals onto the batch and close it out.
     */
    public function closeOut(JobBatch $batch): JobBatch
    {
        $jobs = $batch->jobs()->get();

        $batch->update([
            'actual_total_tokens' => (int) $jobs->sum('token_count'),
            'actual_duration_seconds' => (int) $jobs->sum('actual_duration_sec'),
            'status' => $jobs->isNotEmpty() && $jobs->every(fn ($job) => $job->status === 'completed')
                ? 'completed'
                : 'partially_failed',
            'completed_at' => now(),
        ]);

        return $batch;
    }
}
