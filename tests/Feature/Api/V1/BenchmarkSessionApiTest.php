<?php

namespace Tests\Feature\Api\V1;

use App\Models\BenchmarkSession;
use Illuminate\Database\Eloquent\Model;

class BenchmarkSessionApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return BenchmarkSession::class;
    }

    protected function tableName(): string
    {
        return 'benchmark_sessions';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/benchmark-sessions';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['name' => 'Updated benchmark session'];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['description' => 'Updated benchmark session description'];
    }
}
