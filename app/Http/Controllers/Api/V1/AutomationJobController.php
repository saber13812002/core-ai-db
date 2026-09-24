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
        $paginator = $this->service->listWithFilters(
            $request->input('status'),
            $request->input('source'),
            $this->boolFilter($request, 'is_automatic'),
            $request->input('source_file_id'),
            $request->input('batch_id'),
            $request->integer('per_page', 15),
        );
        $paginator->getCollection()->load($this->eagerLoad());

        return AutomationJobResource::collection($paginator)->response();
    }

    /**
     * Parse a tri-state boolean query param: absent => null (no filter),
     * otherwise the value as a real boolean.
     */
    private function boolFilter(Request $request, string $key): ?bool
    {
        $value = $request->input($key);

        return $value === null || $value === '' ? null : (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function store(StoreAutomationJobRequest $request): Response
    {
        $model = $this->service->create($this->withCreator($request, $request->validated()));
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
