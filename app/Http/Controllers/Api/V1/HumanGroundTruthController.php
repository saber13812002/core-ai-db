<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreHumanGroundTruthRequest;
use App\Http\Requests\Api\V1\UpdateHumanGroundTruthRequest;
use App\Http\Resources\Api\V1\HumanGroundTruthResource;
use App\Models\HumanGroundTruth;
use App\Services\HumanGroundTruthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HumanGroundTruthController extends Controller
{
    public function __construct(protected HumanGroundTruthService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['sourceFile', 'outputType', 'action', 'basedOnOutput', 'basedOnCleaned'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return HumanGroundTruthResource::collection($paginator)->response();
    }

    public function store(StoreHumanGroundTruthRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new HumanGroundTruthResource($model))->response()->setStatusCode(201);
    }

    public function show(HumanGroundTruth $groundTruth): Response
    {
        $model = $this->service->findOrThrow($groundTruth->getKey());
        $model->load($this->eagerLoad());

        return (new HumanGroundTruthResource($model))->response();
    }

    public function update(UpdateHumanGroundTruthRequest $request, HumanGroundTruth $groundTruth): Response
    {
        $model = $this->service->update($groundTruth->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new HumanGroundTruthResource($model))->response();
    }

    public function destroy(HumanGroundTruth $groundTruth): Response
    {
        $this->service->delete($groundTruth->getKey());

        return response(null, 204);
    }
}
