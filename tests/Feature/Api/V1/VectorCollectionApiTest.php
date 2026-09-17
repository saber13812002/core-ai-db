<?php

namespace Tests\Feature\Api\V1;

use App\Models\VectorCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VectorCollectionApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return VectorCollection::class;
    }

    protected function tableName(): string
    {
        return 'vector_collections';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/vector-collections';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['name' => 'vc-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['description' => 'Updated vector collection description'];
    }
}
