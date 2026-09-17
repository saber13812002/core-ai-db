<?php

namespace Tests\Feature\Api\V1;

use App\Models\SourceFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SourceFileApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return SourceFile::class;
    }

    protected function tableName(): string
    {
        return 'source_files';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/files';
    }

    protected function softDeletes(): bool
    {
        return true;
    }

    protected function storeOverrides(Model $record): array
    {
        return ['external_ref' => 'ref-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['page_count' => 42];
    }
}
