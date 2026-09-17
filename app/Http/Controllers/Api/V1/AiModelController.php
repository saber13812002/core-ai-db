<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAiModelRequest;
use App\Http\Requests\Api\V1\UpdateAiModelRequest;
use App\Http\Resources\Api\V1\AiModelResource;
use App\Models\AiModel;
use App\Services\AiModelService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AiModelController extends Controller
{
    public function __construct(protected AiModelService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return AiModelResource::collection($paginator)->response();
    }

    public function store(StoreAiModelRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new AiModelResource($model))->response()->setStatusCode(201);
    }

    public function show(AiModel $model): Response
    {
        $record = $this->service->findOrThrow($model->getKey());

        return (new AiModelResource($record))->response();
    }

    public function update(UpdateAiModelRequest $request, AiModel $model): Response
    {
        $record = $this->service->update($model->getKey(), $request->validated());

        return (new AiModelResource($record))->response();
    }

    public function destroy(AiModel $model): Response
    {
        $this->service->delete($model->getKey());

        return response(null, 204);
    }
}
