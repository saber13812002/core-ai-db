<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAutomationActionRequest;
use App\Http\Requests\Api\V1\UpdateAutomationActionRequest;
use App\Http\Resources\Api\V1\AutomationActionResource;
use App\Models\AutomationAction;
use App\Services\AutomationActionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutomationActionController extends Controller
{
    public function __construct(protected AutomationActionService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['outputType'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return AutomationActionResource::collection($paginator)->response();
    }

    public function store(StoreAutomationActionRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationActionResource($model))->response()->setStatusCode(201);
    }

    public function show(AutomationAction $automationAction): Response
    {
        $model = $this->service->findOrThrow($automationAction->getKey());
        $model->load($this->eagerLoad());

        return (new AutomationActionResource($model))->response();
    }

    public function update(UpdateAutomationActionRequest $request, AutomationAction $automationAction): Response
    {
        $model = $this->service->update($automationAction->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationActionResource($model))->response();
    }

    public function destroy(AutomationAction $automationAction): Response
    {
        $this->service->delete($automationAction->getKey());

        return response(null, 204);
    }
}
