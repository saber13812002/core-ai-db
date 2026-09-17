<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAutomationJobRequest;
use App\Http\Requests\Api\V1\UpdateAutomationJobRequest;
use App\Http\Resources\Api\V1\AutomationJobResource;
use App\Models\AutomationJob;
use App\Services\AutomationJobService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutomationJobController extends Controller
{
    public function __construct(protected AutomationJobService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['sourceFile', 'action', 'flow', 'model', 'prompt', 'cleaningPrompt', 'service'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return AutomationJobResource::collection($paginator)->response();
    }

    public function store(StoreAutomationJobRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationJobResource($model))->response()->setStatusCode(201);
    }

    public function show(AutomationJob $job): Response
    {
        $model = $this->service->findOrThrow($job->getKey());
        $model->load($this->eagerLoad());

        return (new AutomationJobResource($model))->response();
    }

    public function update(UpdateAutomationJobRequest $request, AutomationJob $job): Response
    {
        $model = $this->service->update($job->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new AutomationJobResource($model))->response();
    }

    public function destroy(AutomationJob $job): Response
    {
        $this->service->delete($job->getKey());

        return response(null, 204);
    }
}
