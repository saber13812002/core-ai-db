<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreVectorCollectionRequest;
use App\Http\Requests\Api\V1\UpdateVectorCollectionRequest;
use App\Http\Resources\Api\V1\VectorCollectionResource;
use App\Models\VectorCollection;
use App\Services\VectorCollectionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VectorCollectionController extends Controller
{
    public function __construct(protected VectorCollectionService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return VectorCollectionResource::collection($paginator)->response();
    }

    public function store(StoreVectorCollectionRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new VectorCollectionResource($model))->response()->setStatusCode(201);
    }

    public function show(VectorCollection $vectorCollection): Response
    {
        $model = $this->service->findOrThrow($vectorCollection->getKey());

        return (new VectorCollectionResource($model))->response();
    }

    public function update(UpdateVectorCollectionRequest $request, VectorCollection $vectorCollection): Response
    {
        $model = $this->service->update($vectorCollection->getKey(), $request->validated());

        return (new VectorCollectionResource($model))->response();
    }

    public function destroy(VectorCollection $vectorCollection): Response
    {
        $this->service->delete($vectorCollection->getKey());

        return response(null, 204);
    }
}
