<?php

namespace Tests\Feature\Api\V1;

use App\Models\TrainingJob;
use Illuminate\Database\Eloquent\Model;

class TrainingJobApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return TrainingJob::class;
    }

    protected function tableName(): string
    {
        return 'training_jobs';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/training-jobs';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'estimated_cost_usd' => null,
            'actual_cost_usd' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['progress_percent' => 55];
    }
}
