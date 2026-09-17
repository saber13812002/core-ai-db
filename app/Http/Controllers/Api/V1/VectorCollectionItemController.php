<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreVectorCollectionItemRequest;
use App\Http\Requests\Api\V1\UpdateVectorCollectionItemRequest;
use App\Http\Resources\Api\V1\VectorCollectionItemResource;
use App\Models\VectorCollection;
use App\Models\VectorCollectionItem;
use App\Services\VectorCollectionItemService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VectorCollectionItemController extends Controller
{
    public function __construct(protected VectorCollectionItemService $service) {}

    public function index(Request $request, VectorCollection $vectorCollection): Response
    {
        $paginator = $this->service->listForCollection($vectorCollection->getKey(), $request->integer('per_page', 15));
        $paginator->getCollection()->load(['sourceFile', 'processedOutput', 'cleanedOutput']);

        return VectorCollectionItemResource::collection($paginator)->response();
    }

    public function store(StoreVectorCollectionItemRequest $request, VectorCollection $vectorCollection): Response
    {
        $item = $this->service->createForCollection($vectorCollection->getKey(), $request->validated());
        $item->load(['vectorCollection', 'sourceFile', 'processedOutput', 'cleanedOutput']);

        return (new VectorCollectionItemResource($item))->response()->setStatusCode(201);
    }

    public function show(Request $request, VectorCollection $vectorCollection, VectorCollectionItem $vectorCollectionItem): Response
    {
        $item = $this->service->findOrThrowInCollection($vectorCollection->getKey(), $vectorCollectionItem->getKey());
        $item->load(['vectorCollection', 'sourceFile', 'processedOutput', 'cleanedOutput']);

        return (new VectorCollectionItemResource($item))->response();
    }

    public function update(UpdateVectorCollectionItemRequest $request, VectorCollection $vectorCollection, VectorCollectionItem $vectorCollectionItem): Response
    {
        $item = $this->service->updateInCollection($vectorCollection->getKey(), $vectorCollectionItem->getKey(), $request->validated());
        $item->load(['vectorCollection', 'sourceFile', 'processedOutput', 'cleanedOutput']);

        return (new VectorCollectionItemResource($item))->response();
    }

    public function destroy(VectorCollection $vectorCollection, VectorCollectionItem $vectorCollectionItem): Response
    {
        $this->service->deleteInCollection($vectorCollection->getKey(), $vectorCollectionItem->getKey());

        return response(null, 204);
    }
}
