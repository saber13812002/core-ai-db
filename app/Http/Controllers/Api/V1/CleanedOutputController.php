<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCleanedOutputRequest;
use App\Http\Requests\Api\V1\UpdateCleanedOutputRequest;
use App\Http\Resources\Api\V1\CleanedOutputResource;
use App\Models\CleanedOutput;
use App\Services\CleanedOutputService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanedOutputController extends Controller
{
    public function __construct(protected CleanedOutputService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['processedOutput', 'sourceFile', 'cleaningPrompt', 'model', 'supersededBy'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return CleanedOutputResource::collection($paginator)->response();
    }

    public function store(StoreCleanedOutputRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new CleanedOutputResource($model))->response()->setStatusCode(201);
    }

    public function show(CleanedOutput $cleanedOutput): Response
    {
        $model = $this->service->findOrThrow($cleanedOutput->getKey());
        $model->load($this->eagerLoad());

        return (new CleanedOutputResource($model))->response();
    }

    public function update(UpdateCleanedOutputRequest $request, CleanedOutput $cleanedOutput): Response
    {
        $model = $this->service->update($cleanedOutput->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new CleanedOutputResource($model))->response();
    }

    public function destroy(CleanedOutput $cleanedOutput): Response
    {
        $this->service->delete($cleanedOutput->getKey());

        return response(null, 204);
    }
}
