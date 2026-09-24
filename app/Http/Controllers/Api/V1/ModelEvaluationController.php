<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreModelEvaluationRequest;
use App\Http\Requests\Api\V1\UpdateModelEvaluationRequest;
use App\Http\Resources\Api\V1\ModelEvaluationResource;
use App\Models\ModelEvaluation;
use App\Services\ModelEvaluationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModelEvaluationController extends Controller
{
    public function __construct(protected ModelEvaluationService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['trainedModel', 'benchmarkSession', 'baselineModel', 'judgeModel', 'judgePrompt', 'metricRows'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return ModelEvaluationResource::collection($paginator)->response();
    }

    public function store(StoreModelEvaluationRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new ModelEvaluationResource($model))->response()->setStatusCode(201);
    }

    public function show(ModelEvaluation $modelEvaluation): Response
    {
        $model = $this->service->findOrThrow($modelEvaluation->getKey());
        $model->load($this->eagerLoad());

        return (new ModelEvaluationResource($model))->response();
    }

    public function update(UpdateModelEvaluationRequest $request, ModelEvaluation $modelEvaluation): Response
    {
        $model = $this->service->update($modelEvaluation->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new ModelEvaluationResource($model))->response();
    }

    public function destroy(ModelEvaluation $modelEvaluation): Response
    {
        $this->service->delete($modelEvaluation->getKey());

        return response(null, 204);
    }
}
