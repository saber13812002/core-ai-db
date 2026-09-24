<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreJobBatchRequest;
use App\Http\Requests\Api\V1\UpdateJobBatchRequest;
use App\Http\Resources\Api\V1\JobBatchResource;
use App\Models\JobBatch;
use App\Services\JobBatchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JobBatchController extends Controller
{
    public function __construct(protected JobBatchService $service) {}

    public function index(Request $request): Response
    {
        $paginator = $this->service->listWithFilters($request->input('status'), $request->integer('per_page', 15));
        $paginator->getCollection()->load('masterPrompt');

        return JobBatchResource::collection($paginator)->response();
    }

    public function store(StoreJobBatchRequest $request): Response
    {
        $model = $this->service->createWithEstimate($request->validated());
        $model->load('masterPrompt');

        return (new JobBatchResource($model))->response()->setStatusCode(201);
    }

    public function show(JobBatch $jobBatch): Response
    {
        $model = $this->service->findOrThrow($jobBatch->getKey());
        $model->load('masterPrompt');

        return (new JobBatchResource($model))->response();
    }

    public function update(UpdateJobBatchRequest $request, JobBatch $jobBatch): Response
    {
        $model = $this->service->update($jobBatch->getKey(), $request->validated());
        $model->load('masterPrompt');

        return (new JobBatchResource($model))->response();
    }

    /**
     * POST /job-batches/{jobBatch}/close-out
     *
     * Aggregate the batch's jobs' actuals onto the batch and close it out.
     */
    public function closeOut(JobBatch $jobBatch): Response
    {
        $model = $this->service->closeOut($jobBatch);
        $model->load('masterPrompt');

        return (new JobBatchResource($model))->response();
    }

    public function destroy(JobBatch $jobBatch): Response
    {
        $this->service->delete($jobBatch->getKey());

        return response(null, 204);
    }
}
