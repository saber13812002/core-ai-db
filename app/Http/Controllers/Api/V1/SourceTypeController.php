<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSourceTypeRequest;
use App\Http\Requests\Api\V1\UpdateSourceTypeRequest;
use App\Http\Resources\Api\V1\SourceTypeResource;
use App\Models\SourceType;
use App\Services\SourceTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SourceTypeController extends Controller
{
    public function __construct(protected SourceTypeService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return SourceTypeResource::collection($paginator)->response();
    }

    public function store(StoreSourceTypeRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new SourceTypeResource($model))->response()->setStatusCode(201);
    }

    public function show(SourceType $sourceType): Response
    {
        $model = $this->service->findOrThrow($sourceType->getKey());

        return (new SourceTypeResource($model))->response();
    }

    public function update(UpdateSourceTypeRequest $request, SourceType $sourceType): Response
    {
        $model = $this->service->update($sourceType->getKey(), $request->validated());

        return (new SourceTypeResource($model))->response();
    }

    public function destroy(SourceType $sourceType): Response
    {
        $this->service->delete($sourceType->getKey());

        return response(null, 204);
    }
}
