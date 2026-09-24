<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreIntegrationEventRequest;
use App\Http\Resources\Api\V1\IntegrationEventResource;
use App\Services\IntegrationEventService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Append-only audit of callbacks received from external services. The
 * webhook endpoint validates that the referenced main-table row exists
 * before storing; the list/show endpoints let us replay a file's full
 * status history from every service that reported on it.
 */
class IntegrationEventController extends Controller
{
    public function __construct(protected IntegrationEventService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->listWithFilters(
            $request->input('reference_type'),
            $request->input('reference_id'),
            $request->input('event_type'),
            $request->integer('per_page', 15),
        );
        $paginator->getCollection()->load('service');

        return IntegrationEventResource::collection($paginator)->response();
    }

    /**
     * POST /webhooks/integration-events — an external service (or the
     * Gateway/Orchestrator on its behalf) reports status using a UUID we own.
     */
    public function store(StoreIntegrationEventRequest $request): Response
    {
        $model = $this->service->record($request->validated());
        $model->load('service');

        return (new IntegrationEventResource($model))->response()->setStatusCode(201);
    }

    /**
     * The full event history for one referenced record, newest first.
     */
    public function show(Request $request, string $referenceType, string $referenceId): Response
    {
        $events = $this->service->forReference($referenceType, $referenceId);

        return response()->json([
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'data' => IntegrationEventResource::collection($events),
        ]);
    }
}
