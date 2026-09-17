<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProcessedOutputRequest;
use App\Http\Requests\Api\V1\UpdateProcessedOutputRequest;
use App\Http\Resources\Api\V1\ProcessedOutputResource;
use App\Models\ProcessedOutput;
use App\Services\ProcessedOutputService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessedOutputController extends Controller
{
    public function __construct(protected ProcessedOutputService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['job', 'sourceFile', 'outputType', 'action', 'model', 'prompt', 'supersededBy'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return ProcessedOutputResource::collection($paginator)->response();
    }

    public function store(StoreProcessedOutputRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new ProcessedOutputResource($model))->response()->setStatusCode(201);
    }

    public function show(ProcessedOutput $output): Response
    {
        $model = $this->service->findOrThrow($output->getKey());
        $model->load($this->eagerLoad());

        return (new ProcessedOutputResource($model))->response();
    }

    public function update(UpdateProcessedOutputRequest $request, ProcessedOutput $output): Response
    {
        $model = $this->service->update($output->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new ProcessedOutputResource($model))->response();
    }

    public function destroy(ProcessedOutput $output): Response
    {
        $this->service->delete($output->getKey());

        return response(null, 204);
    }
}
