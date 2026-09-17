<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceCallLogRequest;
use App\Http\Requests\Api\V1\UpdateServiceCallLogRequest;
use App\Http\Resources\Api\V1\ServiceCallLogResource;
use App\Models\ServiceCallLog;
use App\Services\ServiceCallLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceCallLogController extends Controller
{
    public function __construct(protected ServiceCallLogService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['service', 'sourceFile', 'job'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return ServiceCallLogResource::collection($paginator)->response();
    }

    public function store(StoreServiceCallLogRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new ServiceCallLogResource($model))->response()->setStatusCode(201);
    }

    public function show(ServiceCallLog $serviceCallLog): Response
    {
        $model = $this->service->findOrThrow($serviceCallLog->getKey());
        $model->load($this->eagerLoad());

        return (new ServiceCallLogResource($model))->response();
    }

    public function update(UpdateServiceCallLogRequest $request, ServiceCallLog $serviceCallLog): Response
    {
        $model = $this->service->update($serviceCallLog->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new ServiceCallLogResource($model))->response();
    }

    public function destroy(ServiceCallLog $serviceCallLog): Response
    {
        $this->service->delete($serviceCallLog->getKey());

        return response(null, 204);
    }
}
