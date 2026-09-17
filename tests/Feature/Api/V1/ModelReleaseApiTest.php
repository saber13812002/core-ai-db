<?php

namespace Tests\Feature\Api\V1;

use App\Models\ModelRelease;
use Illuminate\Database\Eloquent\Model;

class ModelReleaseApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ModelRelease::class;
    }

    protected function tableName(): string
    {
        return 'model_releases';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/model-releases';
    }

    protected function storeOverrides(Model $record): array
    {
        return [
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'deployed_at' => null,
            'retired_at' => null,
        ];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['release_notes' => 'Updated release notes'];
    }
}
