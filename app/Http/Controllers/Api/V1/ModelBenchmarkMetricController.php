<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreModelBenchmarkMetricRequest;
use App\Http\Requests\Api\V1\UpdateModelBenchmarkMetricRequest;
use App\Http\Resources\Api\V1\ModelBenchmarkMetricResource;
use App\Models\ModelBenchmarkMetric;
use App\Models\ModelEvaluation;
use App\Services\ModelBenchmarkMetricService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModelBenchmarkMetricController extends Controller
{
    public function __construct(protected ModelBenchmarkMetricService $service) {}

    public function index(Request $request, ModelEvaluation $modelEvaluation): Response
    {
        $paginator = $this->service->listForEvaluation($modelEvaluation->getKey(), $request->integer('per_page', 15));
        $paginator->getCollection()->load('metricPrompt');

        return ModelBenchmarkMetricResource::collection($paginator)->response();
    }

    public function store(StoreModelBenchmarkMetricRequest $request, ModelEvaluation $modelEvaluation): Response
    {
        $metric = $this->service->createForEvaluation($modelEvaluation->getKey(), $request->validated());
        $metric->load('metricPrompt');

        return (new ModelBenchmarkMetricResource($metric))->response()->setStatusCode(201);
    }

    public function show(Request $request, ModelEvaluation $modelEvaluation, ModelBenchmarkMetric $modelBenchmarkMetric): Response
    {
        $metric = $this->service->findOrThrowInEvaluation($modelEvaluation->getKey(), $modelBenchmarkMetric->getKey());
        $metric->load('metricPrompt');

        return (new ModelBenchmarkMetricResource($metric))->response();
    }

    public function update(UpdateModelBenchmarkMetricRequest $request, ModelEvaluation $modelEvaluation, ModelBenchmarkMetric $modelBenchmarkMetric): Response
    {
        $metric = $this->service->updateInEvaluation($modelEvaluation->getKey(), $modelBenchmarkMetric->getKey(), $request->validated());
        $metric->load('metricPrompt');

        return (new ModelBenchmarkMetricResource($metric))->response();
    }

    public function destroy(ModelEvaluation $modelEvaluation, ModelBenchmarkMetric $modelBenchmarkMetric): Response
    {
        $this->service->deleteInEvaluation($modelEvaluation->getKey(), $modelBenchmarkMetric->getKey());

        return response(null, 204);
    }
}
