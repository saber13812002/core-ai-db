<?php

namespace Tests\Feature\Api\V1;

use App\Models\SourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SourceTypeApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return SourceType::class;
    }

    protected function tableName(): string
    {
        return 'source_types';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/source-types';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['code' => 'st-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['label_fa' => 'نوع به‌روزشده'];
    }
}
