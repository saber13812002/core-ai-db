<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOutputTypeRequest;
use App\Http\Requests\Api\V1\UpdateOutputTypeRequest;
use App\Http\Resources\Api\V1\OutputTypeResource;
use App\Models\OutputType;
use App\Services\OutputTypeService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OutputTypeController extends Controller
{
    public function __construct(protected OutputTypeService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return OutputTypeResource::collection($paginator)->response();
    }

    public function store(StoreOutputTypeRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new OutputTypeResource($model))->response()->setStatusCode(201);
    }

    public function show(OutputType $outputType): Response
    {
        $model = $this->service->findOrThrow($outputType->getKey());

        return (new OutputTypeResource($model))->response();
    }

    public function update(UpdateOutputTypeRequest $request, OutputType $outputType): Response
    {
        $model = $this->service->update($outputType->getKey(), $request->validated());

        return (new OutputTypeResource($model))->response();
    }

    public function destroy(OutputType $outputType): Response
    {
        $this->service->delete($outputType->getKey());

        return response(null, 204);
    }
}
