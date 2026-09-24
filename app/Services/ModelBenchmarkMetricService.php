<?php

namespace App\Services;

use App\Models\ModelBenchmarkMetric;
use App\Repositories\ModelBenchmarkMetricRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModelBenchmarkMetricService
{
    public function __construct(protected ModelBenchmarkMetricRepository $repository) {}

    public function listForEvaluation(string $modelEvaluationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->listForEvaluation($modelEvaluationId, $perPage);
    }

    public function createForEvaluation(string $modelEvaluationId, array $attributes): ModelBenchmarkMetric
    {
        return $this->repository->createForEvaluation($modelEvaluationId, $attributes);
    }

    public function findOrThrowInEvaluation(string $modelEvaluationId, string|int $id): ModelBenchmarkMetric
    {
        return $this->repository->findOrThrowInEvaluation($modelEvaluationId, $id);
    }

    public function updateInEvaluation(string $modelEvaluationId, string|int $id, array $attributes): ModelBenchmarkMetric
    {
        return $this->repository->updateInEvaluation($modelEvaluationId, $id, $attributes);
    }

    public function deleteInEvaluation(string $modelEvaluationId, string|int $id): void
    {
        $this->repository->deleteInEvaluation($modelEvaluationId, $id);
    }
}
