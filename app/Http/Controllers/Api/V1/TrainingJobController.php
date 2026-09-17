<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTrainingJobRequest;
use App\Http\Requests\Api\V1\UpdateTrainingJobRequest;
use App\Http\Resources\Api\V1\TrainingJobResource;
use App\Models\TrainingJob;
use App\Services\TrainingJobService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrainingJobController extends Controller
{
    public function __construct(protected TrainingJobService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['dataset', 'service'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return TrainingJobResource::collection($paginator)->response();
    }

    public function store(StoreTrainingJobRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new TrainingJobResource($model))->response()->setStatusCode(201);
    }

    public function show(TrainingJob $trainingJob): Response
    {
        $model = $this->service->findOrThrow($trainingJob->getKey());
        $model->load($this->eagerLoad());

        return (new TrainingJobResource($model))->response();
    }

    public function update(UpdateTrainingJobRequest $request, TrainingJob $trainingJob): Response
    {
        $model = $this->service->update($trainingJob->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new TrainingJobResource($model))->response();
    }

    public function destroy(TrainingJob $trainingJob): Response
    {
        $this->service->delete($trainingJob->getKey());

        return response(null, 204);
    }
}
