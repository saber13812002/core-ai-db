<?php

namespace Tests\Feature\Api\V1;

use App\Models\MetadataSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MetadataSchemaApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return MetadataSchema::class;
    }

    protected function tableName(): string
    {
        return 'metadata_schemas';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/metadata-schemas';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['name' => Str::slug(Str::random(12))];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['scope' => 'mp3'];
    }
}
