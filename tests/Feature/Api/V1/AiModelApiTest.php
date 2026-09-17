<?php

namespace Tests\Feature\Api\V1;

use App\Models\AiModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiModelApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return AiModel::class;
    }

    protected function tableName(): string
    {
        return 'models';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/models';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['code' => 'ai-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['name_fa' => 'Updated AI model'];
    }
}
