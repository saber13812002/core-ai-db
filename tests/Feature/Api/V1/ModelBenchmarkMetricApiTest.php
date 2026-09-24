<?php

namespace Tests\Feature\Api\V1;

use App\Models\ModelBenchmarkMetric;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModelBenchmarkMetricApiTest extends CrudApiTestCase
{
    protected function modelClass(): string
    {
        return ModelBenchmarkMetric::class;
    }

    protected function tableName(): string
    {
        return 'model_benchmark_metrics';
    }

    protected function collectionUrl(Model $record): string
    {
        return 'api/v1/model-evaluations/'.$record->model_evaluation_id.'/metrics';
    }

    protected function storeOverrides(Model $record): array
    {
        return ['metric_name' => 'metric-'.Str::random(12)];
    }

    protected function updateFieldsFor(Model $record): array
    {
        return ['score' => 88.5];
    }

    public function test_store_rejects_score_out_of_range(): void
    {
        $metric = $this->createSeedRecord();

        $this->postJson($this->collectionUrl($metric), [
            'metric_name' => 'accuracy',
            'score' => 150,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['score']);
    }

    public function test_scoping_metrics_to_wrong_evaluation_is_404(): void
    {
        $metric = $this->createSeedRecord();

        $this->getJson('api/v1/model-evaluations/'.(string) Str::uuid().'/metrics/'.$metric->id)
            ->assertNotFound();
    }
}
