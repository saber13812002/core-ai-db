<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBenchmarkSessionRequest;
use App\Http\Requests\Api\V1\UpdateBenchmarkSessionRequest;
use App\Http\Resources\Api\V1\BenchmarkSessionResource;
use App\Models\BenchmarkSession;
use App\Services\BenchmarkSessionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BenchmarkSessionController extends Controller
{
    public function __construct(protected BenchmarkSessionService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['sourceFile', 'outputType', 'action', 'judgeModel', 'judgePrompt'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return BenchmarkSessionResource::collection($paginator)->response();
    }

    public function store(StoreBenchmarkSessionRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new BenchmarkSessionResource($model))->response()->setStatusCode(201);
    }

    public function show(BenchmarkSession $benchmarkSession): Response
    {
        $model = $this->service->findOrThrow($benchmarkSession->getKey());
        $model->load($this->eagerLoad());

        return (new BenchmarkSessionResource($model))->response();
    }

    public function update(UpdateBenchmarkSessionRequest $request, BenchmarkSession $benchmarkSession): Response
    {
        $model = $this->service->update($benchmarkSession->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new BenchmarkSessionResource($model))->response();
    }

    public function destroy(BenchmarkSession $benchmarkSession): Response
    {
        $this->service->delete($benchmarkSession->getKey());

        return response(null, 204);
    }
}
