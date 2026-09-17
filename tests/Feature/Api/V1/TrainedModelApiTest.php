<?php

namespace Tests\Feature\Api\V1;

use App\Models\TrainedModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrainedModelApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return TrainedModel::class;
    }

    protected function tableName(): string
    {
        return 'trained_models';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/trained-models';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'training_job_id' => null,
            'name' => 'tm-'.Str::random(12),
            'version' => '2.0.0',
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['service_endpoint' => 'http://localhost:9000/updated'];
    }
}
