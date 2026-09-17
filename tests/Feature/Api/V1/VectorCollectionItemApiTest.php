<?php

namespace Tests\Feature\Api\V1;

use App\Models\VectorCollectionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VectorCollectionItemApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return VectorCollectionItem::class;
    }

    protected function tableName(): string
    {
        return 'vector_collection_items';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/vector-collections/'.$record->vector_collection_id.'/items';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['cleaned_output_id' => null];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['external_vector_id' => (string) Str::uuid()];
    }
}
