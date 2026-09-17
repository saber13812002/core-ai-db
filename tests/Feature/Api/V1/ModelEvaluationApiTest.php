<?php

namespace Tests\Feature\Api\V1;

use App\Models\ModelEvaluation;
use Illuminate\Database\Eloquent\Model;

class ModelEvaluationApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ModelEvaluation::class;
    }

    protected function tableName(): string
    {
        return 'model_evaluations';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/model-evaluations';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'benchmark_session_id' => null,
            'baseline_model_id' => null,
            'improvement_percent' => null,
            'is_better_than_baseline' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['overall_score' => 87.25];
    }
}
