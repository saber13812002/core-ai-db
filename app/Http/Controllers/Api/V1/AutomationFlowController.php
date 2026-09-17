<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAutomationFlowRequest;
use App\Http\Requests\Api\V1\UpdateAutomationFlowRequest;
use App\Http\Resources\Api\V1\AutomationFlowResource;
use App\Models\AutomationFlow;
use App\Services\AutomationFlowService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutomationFlowController extends Controller
{
    public function __construct(protected AutomationFlowService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['outputType', 'action'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return AutomationFlowResource::collection($paginator)->response();
    }

    public function store(StoreAutomationFlowRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationFlowResource($model))->response()->setStatusCode(201);
    }

    public function show(AutomationFlow $automationFlow): Response
    {
        $model = $this->service->findOrThrow($automationFlow->getKey());
        $model->load($this->eagerLoad());

        return (new AutomationFlowResource($model))->response();
    }

    public function update(UpdateAutomationFlowRequest $request, AutomationFlow $automationFlow): Response
    {
        $model = $this->service->update($automationFlow->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationFlowResource($model))->response();
    }

    public function destroy(AutomationFlow $automationFlow): Response
    {
        $this->service->delete($automationFlow->getKey());

        return response(null, 204);
    }
}
