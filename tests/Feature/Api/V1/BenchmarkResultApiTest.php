<?php

namespace Tests\Feature\Api\V1;

use App\Models\BenchmarkResult;
use Illuminate\Database\Eloquent\Model;

class BenchmarkResultApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return BenchmarkResult::class;
    }

    protected function tableName(): string
    {
        return 'benchmark_results';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/benchmark-results';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['candidate_cleaned_id' => null, 'baseline_cleaned_id' => null, 'ground_truth_id' => null];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['overall_score' => 99.5];
    }
}
