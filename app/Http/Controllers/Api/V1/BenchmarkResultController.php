<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBenchmarkResultRequest;
use App\Http\Requests\Api\V1\UpdateBenchmarkResultRequest;
use App\Http\Resources\Api\V1\BenchmarkResultResource;
use App\Models\BenchmarkResult;
use App\Services\BenchmarkResultService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BenchmarkResultController extends Controller
{
    public function __construct(protected BenchmarkResultService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['benchmarkSession', 'candidateOutput', 'candidateCleaned', 'baselineOutput', 'baselineCleaned', 'groundTruth', 'judgeModel', 'judgePrompt'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return BenchmarkResultResource::collection($paginator)->response();
    }

    public function store(StoreBenchmarkResultRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new BenchmarkResultResource($model))->response()->setStatusCode(201);
    }

    public function show(BenchmarkResult $benchmarkResult): Response
    {
        $model = $this->service->findOrThrow($benchmarkResult->getKey());
        $model->load($this->eagerLoad());

        return (new BenchmarkResultResource($model))->response();
    }

    public function update(UpdateBenchmarkResultRequest $request, BenchmarkResult $benchmarkResult): Response
    {
        $model = $this->service->update($benchmarkResult->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new BenchmarkResultResource($model))->response();
    }

    public function destroy(BenchmarkResult $benchmarkResult): Response
    {
        $this->service->delete($benchmarkResult->getKey());

        return response(null, 204);
    }
}
