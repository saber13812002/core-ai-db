<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFeedbackLogRequest;
use App\Http\Requests\Api\V1\UpdateFeedbackLogRequest;
use App\Http\Resources\Api\V1\FeedbackLogResource;
use App\Models\FeedbackLog;
use App\Services\FeedbackLogService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedbackLogController extends Controller
{
    public function __construct(protected FeedbackLogService $service) {}

    /**
     * @return array<int, string>
     */
    private function eagerLoad(): array
    {
        return ['sourceFile', 'processedOutput', 'cleanedOutput', 'trainedModel', 'prompt', 'model'];
    }

    public function index(Request $request): Response
    {
        $paginator = $this->service->list($request->integer('per_page', 15));
        $paginator->getCollection()->load($this->eagerLoad());

        return FeedbackLogResource::collection($paginator)->response();
    }

    public function store(StoreFeedbackLogRequest $request): Response
    {
        $model = $this->service->create($request->validated());
        $model->load($this->eagerLoad());

        return (new FeedbackLogResource($model))->response()->setStatusCode(201);
    }

    public function show(FeedbackLog $feedback): Response
    {
        $model = $this->service->findOrThrow($feedback->getKey());
        $model->load($this->eagerLoad());

        return (new FeedbackLogResource($model))->response();
    }

    public function update(UpdateFeedbackLogRequest $request, FeedbackLog $feedback): Response
    {
        $model = $this->service->update($feedback->getKey(), $request->validated());
        $model->load($this->eagerLoad());

        return (new FeedbackLogResource($model))->response();
    }

    public function destroy(FeedbackLog $feedback): Response
    {
        $this->service->delete($feedback->getKey());

        return response(null, 204);
    }
}
