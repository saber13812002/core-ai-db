<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMetadataSchemaRequest;
use App\Http\Requests\Api\V1\UpdateMetadataSchemaRequest;
use App\Http\Resources\Api\V1\MetadataSchemaResource;
use App\Models\MetadataSchema;
use App\Services\MetadataSchemaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MetadataSchemaController extends Controller
{
    public function __construct(protected MetadataSchemaService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return MetadataSchemaResource::collection($paginator)->response();
    }

    public function store(StoreMetadataSchemaRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new MetadataSchemaResource($model))->response()->setStatusCode(201);
    }

    public function show(MetadataSchema $metadataSchema): Response
    {
        $model = $this->service->findOrThrow($metadataSchema->getKey());

        return (new MetadataSchemaResource($model))->response();
    }

    public function update(UpdateMetadataSchemaRequest $request, MetadataSchema $metadataSchema): Response
    {
        $model = $this->service->update($metadataSchema->getKey(), $request->validated());

        return (new MetadataSchemaResource($model))->response();
    }

    public function destroy(MetadataSchema $metadataSchema): Response
    {
        $this->service->delete($metadataSchema->getKey());

        return response(null, 204);
    }
}
