<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreDatasetItemRequest;
use App\Http\Requests\Api\V1\UpdateDatasetItemRequest;
use App\Http\Resources\Api\V1\DatasetItemResource;
use App\Models\DatasetItem;
use App\Services\DatasetItemService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatasetItemController extends Controller
{
    public function __construct(protected DatasetItemService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['dataset', 'sourceFile', 'inputOutput', 'inputCleaned', 'groundTruth'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return DatasetItemResource::collection($paginator)->response();
    }

    public function store(StoreDatasetItemRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new DatasetItemResource($model))->response()->setStatusCode(201);
    }

    public function show(DatasetItem $datasetItem): Response
    {
        $model = $this->service->findOrThrow($datasetItem->getKey());
        $model->load($this->eagerLoad());

        return (new DatasetItemResource($model))->response();
    }

    public function update(UpdateDatasetItemRequest $request, DatasetItem $datasetItem): Response
    {
        $model = $this->service->update($datasetItem->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new DatasetItemResource($model))->response();
    }

    public function destroy(DatasetItem $datasetItem): Response
    {
        $this->service->delete($datasetItem->getKey());

        return response(null, 204);
    }
}
