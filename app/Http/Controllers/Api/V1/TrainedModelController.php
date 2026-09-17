<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTrainedModelRequest;
use App\Http\Requests\Api\V1\UpdateTrainedModelRequest;
use App\Http\Resources\Api\V1\TrainedModelResource;
use App\Models\TrainedModel;
use App\Services\TrainedModelService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainedModelController extends Controller
{
    public function __construct(protected TrainedModelService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['trainingJob'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return TrainedModelResource::collection($paginator)->response();
    }

    public function store(StoreTrainedModelRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new TrainedModelResource($model))->response()->setStatusCode(201);
    }

    public function show(TrainedModel $trainedModel): Response
    {
        $model = $this->service->findOrThrow($trainedModel->getKey());
        $model->load($this->eagerLoad());

        return (new TrainedModelResource($model))->response();
    }

    public function update(UpdateTrainedModelRequest $request, TrainedModel $trainedModel): Response
    {
        $model = $this->service->update($trainedModel->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new TrainedModelResource($model))->response();
    }

    public function destroy(TrainedModel $trainedModel): Response
    {
        $this->service->delete($trainedModel->getKey());

        return response(null, 204);
    }
}
