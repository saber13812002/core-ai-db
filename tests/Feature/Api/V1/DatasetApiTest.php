<?php

namespace Tests\Feature\Api\V1;

use App\Models\Dataset;
use Illuminate\Database\Eloquent\Model;

class DatasetApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return Dataset::class;
    }

    protected function tableName(): string
    {
        return 'datasets';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/datasets';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'name' => 'Updated dataset',
            'created_by' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['description' => 'Updated dataset description'];
    }
}
