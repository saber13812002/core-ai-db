<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreModelReleaseRequest;
use App\Http\Requests\Api\V1\UpdateModelReleaseRequest;
use App\Http\Resources\Api\V1\ModelReleaseResource;
use App\Models\ModelRelease;
use App\Services\ModelReleaseService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModelReleaseController extends Controller
{
    public function __construct(protected ModelReleaseService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['trainedModel'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return ModelReleaseResource::collection($paginator)->response();
    }

    public function store(StoreModelReleaseRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new ModelReleaseResource($model))->response()->setStatusCode(201);
    }

    public function show(ModelRelease $modelRelease): Response
    {
        $model = $this->service->findOrThrow($modelRelease->getKey());
        $model->load($this->eagerLoad());

        return (new ModelReleaseResource($model))->response();
    }

    public function update(UpdateModelReleaseRequest $request, ModelRelease $modelRelease): Response
    {
        $model = $this->service->update($modelRelease->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new ModelReleaseResource($model))->response();
    }

    public function destroy(ModelRelease $modelRelease): Response
    {
        $this->service->delete($modelRelease->getKey());

        return response(null, 204);
    }
}
