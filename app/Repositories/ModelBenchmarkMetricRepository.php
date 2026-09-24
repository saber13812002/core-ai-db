<?php

namespace App\Repositories;

use App\Models\ModelBenchmarkMetric;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ModelBenchmarkMetricRepository extends BaseRepository
{
    public function __construct(ModelBenchmarkMetric $model)
    {
        parent::__construct($model);
    }

    public function listForEvaluation(string $modelEvaluationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->where('model_evaluation_id', $modelEvaluationId)
            ->orderBy('evaluated_at')
            ->paginate($perPage);
    }

    public function createForEvaluation(string $modelEvaluationId, array $attributes): ModelBenchmarkMetric
    {
        return $this->query()->create($attributes + ['model_evaluation_id' => $modelEvaluationId]);
    }

    public function findInEvaluation(string $modelEvaluationId, string|int $id): ?ModelBenchmarkMetric
    {
        return $this->query()
            ->where('model_evaluation_id', $modelEvaluationId)
            ->whereKey($id)
            ->first();
    }

    public function findOrThrowInEvaluation(string $modelEvaluationId, string|int $id): ModelBenchmarkMetric
    {
        return $this->query()
            ->where('model_evaluation_id', $modelEvaluationId)
            ->whereKey($id)
            ->firstOrFail();
    }

    public function updateInEvaluation(string $modelEvaluationId, string|int $id, array $attributes): ModelBenchmarkMetric
    {
        $record = $this->findOrThrowInEvaluation($modelEvaluationId, $id);

        $record->update($attributes);

        return $record;
    }

    public function deleteInEvaluation(string $modelEvaluationId, string|int $id): void
    {
        $this->findOrThrowInEvaluation($modelEvaluationId, $id)->delete();
    }
}
