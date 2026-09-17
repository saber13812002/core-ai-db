<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceRegistryRequest;
use App\Http\Requests\Api\V1\UpdateServiceRegistryRequest;
use App\Http\Resources\Api\V1\ServiceRegistryResource;
use App\Models\ServiceRegistry;
use App\Services\ServiceRegistryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceRegistryController extends Controller
{
    public function __construct(protected ServiceRegistryService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));

        return ServiceRegistryResource::collection($paginator)->response();
    }

    public function store(StoreServiceRegistryRequest $request): Response
    {
        $model = $this->service->create($request->validated());

        return (new ServiceRegistryResource($model))->response()->setStatusCode(201);
    }

    public function show(ServiceRegistry $service): Response
    {
        $record = $this->service->findOrThrow($service->getKey());

        return (new ServiceRegistryResource($record))->response();
    }

    public function update(UpdateServiceRegistryRequest $request, ServiceRegistry $service): Response
    {
        $record = $this->service->update($service->getKey(), $request->validated());

        return (new ServiceRegistryResource($record))->response();
    }

    public function destroy(ServiceRegistry $service): Response
    {
        $this->service->delete($service->getKey());

        return response(null, 204);
    }
}
