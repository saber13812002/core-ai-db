<?php

namespace Tests\Feature\Api\V1;

use App\Models\OutputType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OutputTypeApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return OutputType::class;
    }

    protected function tableName(): string
    {
        return 'output_types';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/output-types';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['code' => 'ot-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['name_fa' => 'Updated output type'];
    }
}
