<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreDatasetRequest;
use App\Http\Requests\Api\V1\UpdateDatasetRequest;
use App\Http\Resources\Api\V1\DatasetResource;
use App\Models\Dataset;
use App\Services\DatasetService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DatasetController extends Controller
{
    public function __construct(protected DatasetService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['targetOutputType'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return DatasetResource::collection($paginator)->response();
    }

    public function store(StoreDatasetRequest $request): Response
    {
        $model = $this->service->create($this->withCreator($request, $request->validated()));
        $model->load($this->eagerLoad());

        return (new DatasetResource($model))->response()->setStatusCode(201);
    }

    public function show(Dataset $dataset): Response
    {
        $model = $this->service->findOrThrow($dataset->getKey());
        $model->load($this->eagerLoad());

        return (new DatasetResource($model))->response();
    }

    public function update(UpdateDatasetRequest $request, Dataset $dataset): Response
    {
        $model = $this->service->update($dataset->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new DatasetResource($model))->response();
    }

    public function destroy(Dataset $dataset): Response
    {
        $this->service->delete($dataset->getKey());

        return response(null, 204);
    }
}
